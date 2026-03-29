<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDonationJob;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Streamer;
use App\Services\ProfanityFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DonationController extends Controller
{
    /**
     * Tampilkan form donasi publik untuk streamer berdasarkan slug
     */
    public function show(string $slug): View
    {
        $streamer = $this->findStreamerBySlug($slug);

        abort_unless($streamer->is_accepting_donation, 404, 'Streamer sedang tidak menerima donasi.');

        // Load active milestones yang belum completed
        $milestones = $streamer->milestones()
            ->active()
            ->notCompleted()
            ->orderBy('order')
            ->get();

        // Media duration settings for frontend
        $mediaDurationTiers = $streamer->getMediaDurationTiers();
        $mediaUploadEnabled = $streamer->media_upload_enabled ?? true;
        $mediaMaxSizeMb = $streamer->media_max_size_mb ?? 50;

        return view('donate.show', compact(
            'streamer', 
            'milestones', 
            'mediaDurationTiers',
            'mediaUploadEnabled',
            'mediaMaxSizeMb'
        ));
    }

    /**
     * Proses donasi baru (POST dari form publik)
     *
     * Strategi error:
     *  - Error validasi / input user  → 422, JSON errors, tidak simpan apa pun
     *  - Error sistem (DB alert queue) → donasi tetap tersimpan, job di-queue ulang
     *    untuk retry otomatis; donor tetap mendapat konfirmasi positif
     */
    public function store(Request $request, string $slug): JsonResponse
    {
        $streamer = $this->findStreamerBySlug($slug);

        abort_unless($streamer->is_accepting_donation, 403, 'Streamer sedang tidak menerima donasi.');

        // ── Validasi input (error user / pihak ke-3 → rollback otomatis oleh Laravel) ──
        $maxAmount = config('donation.max_amount', 100000000);
        $mediaMaxSizeMb = $streamer->media_max_size_mb ?? 50;
        $mediaMaxSizeKb = $mediaMaxSizeMb * 1024;
        
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:60'],
            'amount' => ['required', 'integer', 'min:' . $streamer->min_donation, 'max:' . $maxAmount],
            'milestone_id' => ['nullable', 'integer', 'exists:milestones,id'],
            'emoji'  => ['nullable', 'string', 'max:10'],
            'msg'    => ['nullable', 'string', 'max:200'],
            'yt_url' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//i',
            ],
            'media_file' => [
                'nullable',
                'file',
                'mimes:mp4,webm,mov,avi,mp3,wav,ogg,m4a',
                'max:' . $mediaMaxSizeKb,
            ],
        ], [
            'name.required'   => 'Nama wajib diisi.',
            'amount.required' => 'Jumlah donasi wajib diisi.',
            'amount.min'      => 'Minimum donasi adalah Rp ' . number_format($streamer->min_donation, 0, ',', '.'),
            'amount.max'      => 'Maksimum donasi adalah Rp ' . number_format($maxAmount, 0, ',', '.'),
            'yt_url.regex'    => 'URL YouTube tidak valid. Gunakan youtube.com atau youtu.be',
            'milestone_id.exists' => 'Milestone tidak valid.',
            'media_file.max'  => 'Ukuran file maksimal ' . $mediaMaxSizeMb . 'MB.',
            'media_file.mimes' => 'Format file tidak didukung. Gunakan MP4, WebM, MOV, AVI, MP3, WAV, OGG, atau M4A.',
        ]);

        // ── Handle media file upload with duration validation ──
        $mediaPath = null;
        if ($request->hasFile('media_file') && $request->file('media_file')->isValid()) {
            // Check if media upload is enabled for this streamer
            if (!($streamer->media_upload_enabled ?? true)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['media_file' => ['Upload media tidak diaktifkan untuk streamer ini.']],
                ], 422);
            }

            // Check if donation amount qualifies for media upload
            $amount = (int) $validated['amount'];
            $maxDuration = $streamer->getMaxMediaDuration($amount);
            
            if ($maxDuration <= 0) {
                $minTier = collect($streamer->getMediaDurationTiers())->sortBy('min_amount')->first();
                $minAmount = $minTier ? $minTier['min_amount'] : 10000;
                return response()->json([
                    'success' => false,
                    'errors' => ['media_file' => ['Donasi minimal Rp ' . number_format($minAmount, 0, ',', '.') . ' untuk upload media.']],
                ], 422);
            }

            // Validate actual file duration using getID3
            $fileDuration = $this->getMediaDuration($request->file('media_file'));
            
            if ($fileDuration === null) {
                return response()->json([
                    'success' => false,
                    'errors' => ['media_file' => ['Tidak dapat membaca durasi file. Pastikan file tidak corrupt.']],
                ], 422);
            }

            if ($fileDuration > $maxDuration) {
                return response()->json([
                    'success' => false,
                    'errors' => ['media_file' => [
                        'Durasi file (' . $this->formatDuration($fileDuration) . ') melebihi batas maksimal (' . $this->formatDuration($maxDuration) . ') untuk nominal donasi ini.'
                    ]],
                ], 422);
            }

            // Upload the file
            try {
                $file = $request->file('media_file');
                $ext = $file->getClientOriginalExtension();
                $filename = 'media_' . $streamer->id . '_' . time() . '_' . Str::random(8) . '.' . $ext;
                $mediaPath = $file->storeAs('donations/media', $filename, 'public');
                
                if ($mediaPath === false) {
                    throw new \RuntimeException('Failed to store media file.');
                }
            } catch (\Throwable $e) {
                Log::error('DonationController: gagal upload media', [
                    'streamer_id' => $streamer->id,
                    'error' => $e->getMessage(),
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => ['media_file' => ['Gagal mengupload file. Mohon coba lagi.']],
                ], 422);
            }
        }

        // ── Sanitasi ──
        $name  = strip_tags($validated['name']);
        $msg   = isset($validated['msg']) ? strip_tags($validated['msg']) : null;
        $ytUrl = ($streamer->yt_enabled && isset($validated['yt_url'])) ? $validated['yt_url'] : null;
        $emoji = $validated['emoji'] ?? '💝';

        // ── Filter konten (sensor kata terlarang / judol / kata kasar) ──
        // Donasi tetap diproses; teks yang tampil di OBS sudah tersensor.
        $filter = app(ProfanityFilter::class);
        $name   = $filter->filter($name, $streamer->id);
        $msg    = $msg !== null ? $filter->filter($msg, $streamer->id) : null;

        // ── Simpan donasi ke DB ──
        // Ini adalah operasi inti; jika gagal berarti DB bermasalah serius — return error.
        try {
            $donation = Donation::create([
                'streamer_id' => $streamer->id,
                'milestone_id' => $validated['milestone_id'] ?? null,
                'name'        => $name,
                'amount'      => (int) $validated['amount'],
                'emoji'       => $emoji,
                'message'     => $msg,
                'yt_url'      => $ytUrl,
                'media_path'  => $mediaPath,
                'ip_address'  => $request->ip(),
            ]);

            // ── Update milestone progress jika ada ──
            if (isset($validated['milestone_id'])) {
                $milestone = \App\Models\Milestone::find($validated['milestone_id']);
                if ($milestone && $milestone->streamer_id === $streamer->id) {
                    $milestone->addAmount((int) $validated['amount']);
                }
            }

            // ── Update Subathon timer jika enabled ──
            $subathonUpdate = null;
            if ($streamer->subathon_enabled) {
                $subathonUpdate = $streamer->addSubathonTime((int) $validated['amount']);
            }
        } catch (\Throwable $e) {
            // If donation save fails, clean up uploaded media
            if ($mediaPath) {
                Storage::disk('public')->delete($mediaPath);
            }
            
            Log::error('DonationController: gagal menyimpan donasi', [
                'streamer_id' => $streamer->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses donasi saat ini. Mohon coba beberapa saat lagi.',
            ], 500);
        }

        // ── Dispatch job untuk membuat alert queue ──
        // Strategi: coba sync dulu (agar alert langsung masuk queue).
        // Jika sync gagal (error sistem, bukan error user), donasi TETAP tersimpan
        // dan job di-dispatch ke queue untuk di-retry otomatis.
        $alertQueued = true;
        try {
            ProcessDonationJob::dispatchSync($donation);
        } catch (\Throwable $e) {
            $alertQueued = false;

            Log::error('DonationController: ProcessDonationJob sync gagal, fallback ke queue', [
                'donation_id' => $donation->id,
                'error'       => $e->getMessage(),
            ]);

            // Fallback: dispatch ke queue worker agar bisa di-retry
            // Job sudah punya $tries=3 dan backoff, jadi akan dicoba ulang otomatis
            try {
                ProcessDonationJob::dispatch($donation)->delay(now()->addSeconds(5));
            } catch (\Throwable $queueError) {
                // Queue juga tidak tersedia — catat ke log tapi donasi tetap aman
                Log::critical('DonationController: fallback queue juga gagal', [
                    'donation_id' => $donation->id,
                    'error'       => $queueError->getMessage(),
                ]);
            }
        }

        // ── Log activity (best-effort, tidak boleh gagalkan response) ──
        try {
            ActivityLog::log(
                action: 'donation.create',
                description: "{$name} berdonasi Rp " . number_format($donation->amount, 0, ',', '.'),
                streamerId: $streamer->id,
                payload: ['donation_id' => $donation->id, 'alert_queued' => $alertQueued]
            );
        } catch (\Throwable $e) {
            Log::warning('DonationController: gagal menyimpan activity log', [
                'donation_id' => $donation->id,
                'error'       => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $streamer->thank_you_message,
            'data'    => ['id' => $donation->id],
        ]);
    }

    /**
     * Get media file duration in seconds using getID3.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return int|null Duration in seconds, or null if cannot read
     */
    private function getMediaDuration($file): ?int
    {
        try {
            // Require getID3 library (legacy, not autoloaded)
            $getID3Path = base_path('vendor/james-heinrich/getid3/getid3/getid3.php');
            if (!class_exists('getID3') && file_exists($getID3Path)) {
                require_once $getID3Path;
            }
            
            if (!class_exists('getID3')) {
                Log::warning('DonationController: getID3 class not found');
                return null;
            }
            
            $getID3 = new \getID3();
            $fileInfo = $getID3->analyze($file->getRealPath());
            
            if (isset($fileInfo['playtime_seconds'])) {
                return (int) ceil($fileInfo['playtime_seconds']);
            }
            
            return null;
        } catch (\Throwable $e) {
            Log::warning('DonationController: getID3 failed to analyze file', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Format duration in seconds to human-readable string.
     *
     * @param int $seconds
     * @return string
     */
    private function formatDuration(int $seconds): string
    {
        if ($seconds >= 60) {
            $mins = floor($seconds / 60);
            $secs = $seconds % 60;
            return $secs > 0 ? "{$mins} menit {$secs} detik" : "{$mins} menit";
        }
        return "{$seconds} detik";
    }
}
