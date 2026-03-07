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
use Illuminate\View\View;

class DonationController extends Controller
{
    /**
     * Tampilkan form donasi publik untuk streamer berdasarkan slug
     */
    public function show(string $slug): View
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();

        abort_unless($streamer->is_accepting_donation, 404, 'Streamer sedang tidak menerima donasi.');

        return view('donate.show', compact('streamer'));
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
        $streamer = Streamer::where('slug', $slug)->firstOrFail();

        abort_unless($streamer->is_accepting_donation, 403, 'Streamer sedang tidak menerima donasi.');

        // ── Validasi input (error user / pihak ke-3 → rollback otomatis oleh Laravel) ──
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:60'],
            'amount' => ['required', 'integer', 'min:' . $streamer->min_donation],
            'emoji'  => ['nullable', 'string', 'max:10'],
            'msg'    => ['nullable', 'string', 'max:200'],
            'yt_url' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//i',
            ],
        ], [
            'name.required'   => 'Nama wajib diisi.',
            'amount.required' => 'Jumlah donasi wajib diisi.',
            'amount.min'      => 'Minimum donasi adalah Rp ' . number_format($streamer->min_donation, 0, ',', '.'),
            'yt_url.regex'    => 'URL YouTube tidak valid. Gunakan youtube.com atau youtu.be',
        ]);

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
                'name'        => $name,
                'amount'      => (int) $validated['amount'],
                'emoji'       => $emoji,
                'message'     => $msg,
                'yt_url'      => $ytUrl,
                'ip_address'  => $request->ip(),
            ]);
        } catch (\Throwable $e) {
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
            'id'      => $donation->id,
        ]);
    }
}
