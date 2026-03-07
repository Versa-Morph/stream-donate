<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AlertQueue;
use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StreamerDashboardController extends Controller
{
    /**
     * Dashboard utama streamer
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Jika belum punya profil streamer, redirect ke setup
        if (!$user->streamer) {
            return redirect()->route('streamer.setup');
        }

        $streamer  = $user->streamer;
        $stats     = $streamer->buildStats();

        // Donasi terbaru (50 terakhir)
        $donations = $streamer->donations()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        // Heatmap: kirim data bulan ini langsung (navigasi bulan lain via AJAX)
        $heatmapInitial = $this->buildMonthHeatmap($streamer, now()->year, now()->month);

        return view('streamer.dashboard', compact('streamer', 'stats', 'donations', 'heatmapInitial'));
    }

    /**
     * Form setup profil streamer baru
     */
    public function setup(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->streamer) {
            return redirect()->route('streamer.dashboard');
        }

        return view('streamer.setup');
    }

    /**
     * Simpan setup profil streamer baru
     */
    public function storeSetup(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->streamer) {
            return redirect()->route('streamer.dashboard');
        }

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:60'],
            'slug'         => ['required', 'string', 'max:40', 'unique:streamers,slug', 'regex:/^[a-z0-9\-]+$/'],
            'bio'          => ['nullable', 'string', 'max:200'],
        ], [
            'slug.unique'  => 'Slug sudah digunakan. Pilih nama lain.',
            'slug.regex'   => 'Slug hanya boleh huruf kecil, angka, dan tanda hubung (-).',
        ]);

        Streamer::create([
            'user_id'      => $user->id,
            'slug'         => $validated['slug'],
            'display_name' => $validated['display_name'],
            'api_key'      => Streamer::generateApiKey(),
            'bio'          => $validated['bio'] ?? null,
        ]);

        ActivityLog::log(
            action: 'streamer.setup',
            description: "Profil streamer {$validated['display_name']} dibuat",
            userId: $user->id,
        );

        return redirect()->route('streamer.dashboard')
            ->with('success', 'Profil streamer berhasil dibuat!');
    }

    /**
     * Halaman settings streamer
     */
    public function settings(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->streamer) {
            return redirect()->route('streamer.setup');
        }

        $streamer = $user->streamer;
        return view('streamer.settings', compact('streamer'));
    }

    /**
     * Simpan perubahan settings
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return redirect()->route('streamer.setup');
        }

        $validated = $request->validate([
            'display_name'         => ['required', 'string', 'max:60'],
            'bio'                  => ['nullable', 'string', 'max:200'],
            'min_donation'         => ['required', 'integer', 'min:100', 'max:1000000'],
            'alert_duration'       => ['required', 'integer', 'min:3000', 'max:30000'],
            'alert_theme'          => ['required', 'in:default,minimal,neon,retro,fire,ice'],
            'yt_enabled'           => ['boolean'],
            'sound_enabled'        => ['boolean'],
            'notification_sound_preset' => ['nullable', 'string', 'in:ding,coin,whoosh,__custom__'],
            'avatar'               => ['nullable', 'image', 'max:2048'],
            'sound_file'           => ['nullable', 'file', 'mimes:mp3,wav,ogg', 'max:5120'],
            'delete_sound'         => ['nullable', 'in:0,1'],
            'milestone_title'      => ['required', 'string', 'max:80'],
            'milestone_target'     => ['required', 'integer', 'min:1000'],
            'milestone_reset'      => ['boolean'],
            'leaderboard_title'    => ['required', 'string', 'max:80'],
            'leaderboard_count'    => ['required', 'integer', 'min:3', 'max:20'],
            'is_accepting_donation'=> ['boolean'],
            'thank_you_message'    => ['required', 'string', 'max:200'],
        ]);

        // ── Handle avatar upload (atomic: upload baru dulu, hapus lama hanya jika berhasil) ──
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            try {
                $ext     = $request->file('avatar')->getClientOriginalExtension();
                $newPath = $request->file('avatar')->storeAs('avatars', $streamer->id . '.' . $ext, 'public');

                if ($newPath === false) {
                    throw new \RuntimeException('Upload file avatar gagal (storeAs returned false).');
                }

                // Upload baru berhasil — baru sekarang hapus file lama
                $oldAvatar = $streamer->avatar;
                $streamer->avatar = $newPath;

                if ($oldAvatar && $oldAvatar !== $newPath) {
                    Storage::disk('public')->delete($oldAvatar);
                }
            } catch (\Throwable $e) {
                Log::error('StreamerDashboardController: gagal upload avatar', [
                    'streamer_id' => $streamer->id,
                    'error'       => $e->getMessage(),
                ]);
                return back()->withInput()->with('error', 'Gagal mengunggah foto profil. Mohon coba lagi.');
            }
        }

        // ── Handle sound file upload / delete (atomic: upload baru dulu, hapus lama hanya jika berhasil) ──
        $existingIsCustom = $streamer->notification_sound
            && !in_array($streamer->notification_sound, ['ding', 'coin', 'whoosh']);
        $submittedPreset  = $request->input('notification_sound_preset', 'ding');
        $userChosePreset  = in_array($submittedPreset, ['ding', 'coin', 'whoosh']);

        if ($request->input('delete_sound') === '1' && $existingIsCustom) {
            // User klik delete — hapus custom sound, kembali ke preset default
            try {
                Storage::disk('public')->delete($streamer->notification_sound);
            } catch (\Throwable $e) {
                Log::warning('StreamerDashboardController: gagal menghapus sound lama', [
                    'streamer_id' => $streamer->id,
                    'path'        => $streamer->notification_sound,
                    'error'       => $e->getMessage(),
                ]);
                // Lanjutkan meski hapus gagal — set ke preset ding agar fungsional
            }
            $notificationSound = 'ding';

        } elseif ($request->hasFile('sound_file') && $request->file('sound_file')->isValid()) {
            // Upload sound baru — upload dulu, baru hapus yang lama
            try {
                $ext     = $request->file('sound_file')->getClientOriginalExtension();
                $newPath = $request->file('sound_file')->storeAs('sounds', $streamer->id . '.' . $ext, 'public');

                if ($newPath === false) {
                    throw new \RuntimeException('Upload file sound gagal (storeAs returned false).');
                }

                // Upload berhasil — baru hapus file lama
                if ($existingIsCustom && $streamer->notification_sound !== $newPath) {
                    try {
                        Storage::disk('public')->delete($streamer->notification_sound);
                    } catch (\Throwable $e) {
                        Log::warning('StreamerDashboardController: gagal hapus sound lama setelah upload baru', [
                            'streamer_id' => $streamer->id,
                            'old_path'    => $streamer->notification_sound,
                            'error'       => $e->getMessage(),
                        ]);
                        // Lanjutkan — file baru sudah tersimpan, file lama jadi orphan (bisa di-cleanup nanti)
                    }
                }
                $notificationSound = $newPath;

            } catch (\Throwable $e) {
                Log::error('StreamerDashboardController: gagal upload sound file', [
                    'streamer_id' => $streamer->id,
                    'error'       => $e->getMessage(),
                ]);
                return back()->withInput()->with('error', 'Gagal mengunggah file suara. Mohon coba lagi.');
            }

        } elseif ($userChosePreset) {
            // User memilih preset built-in — hapus custom file jika ada
            if ($existingIsCustom) {
                try {
                    Storage::disk('public')->delete($streamer->notification_sound);
                } catch (\Throwable $e) {
                    Log::warning('StreamerDashboardController: gagal hapus sound lama saat ganti preset', [
                        'streamer_id' => $streamer->id,
                        'error'       => $e->getMessage(),
                    ]);
                }
            }
            $notificationSound = $submittedPreset;

        } elseif ($existingIsCustom) {
            // Custom badge masih aktif, tidak ada perubahan — pertahankan
            $notificationSound = $streamer->notification_sound;
        } else {
            $notificationSound = 'ding';
        }

        $streamer->update([
            'display_name'          => $validated['display_name'],
            'bio'                   => $validated['bio'] ?? null,
            'min_donation'          => $validated['min_donation'],
            'alert_duration'        => $validated['alert_duration'],
            'alert_theme'           => $validated['alert_theme'],
            'yt_enabled'            => $request->boolean('yt_enabled'),
            'sound_enabled'         => $request->boolean('sound_enabled'),
            'notification_sound'    => $notificationSound,
            'milestone_title'       => $validated['milestone_title'],
            'milestone_target'      => $validated['milestone_target'],
            'milestone_reset'       => $request->boolean('milestone_reset'),
            'leaderboard_title'     => $validated['leaderboard_title'],
            'leaderboard_count'     => $validated['leaderboard_count'],
            'is_accepting_donation' => $request->boolean('is_accepting_donation'),
            'thank_you_message'     => $validated['thank_you_message'],
        ]);

        // Save avatar separately if updated
        if ($streamer->isDirty('avatar')) {
            $streamer->save();
        }

        ActivityLog::log(
            action: 'streamer.settings.updated',
            description: "Settings streamer {$streamer->display_name} diperbarui",
            streamerId: $streamer->id,
        );

        return back()->with('success', 'Settings berhasil disimpan!');
    }

    /**
     * Regenerate API key streamer
     */
    public function regenerateApiKey(): RedirectResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return redirect()->route('streamer.setup');
        }

        $streamer->update(['api_key' => Streamer::generateApiKey()]);

        ActivityLog::log(
            action: 'streamer.apikey.regenerated',
            description: "API key streamer {$streamer->display_name} di-regenerate",
            streamerId: $streamer->id,
        );

        return back()->with('success', 'API key berhasil di-regenerate! Update URL widget OBS kamu.');
    }

    /**
     * AJAX: kembalikan data heatmap untuk bulan tertentu (JSON)
     * GET /streamer/heatmap-data?year=2026&month=3
     */
    public function heatmapData(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['error' => 'Streamer tidak ditemukan.'], 404);
        }

        $year  = (int) $request->query('year',  now()->year);
        $month = (int) $request->query('month', now()->month);

        // Sanity clamp
        if ($year < 2000 || $year > 2100) $year  = now()->year;
        if ($month < 1   || $month > 12)  $month = now()->month;

        return response()->json($this->buildMonthHeatmap($streamer, $year, $month));
    }

    /**
     * Build heatmap data for a given month.
     *
     * Returns:
     *   year          int
     *   month         int
     *   monthLabel    string  e.g. "Maret 2026"
     *   firstWeekday  int     0 = Sun … 6 = Sat (day-of-week of 1st of month)
     *   daysInMonth   int
     *   days          array   [{iso, dateLabel, total, count}, …]  (1 entry per day)
     */
    private function buildMonthHeatmap($streamer, int $year, int $month): array
    {
        $start = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth()->endOfDay();

        // Single aggregated query — date in streamer's app timezone
        $rows = \App\Models\Donation::where('streamer_id', $streamer->id)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("DATE(created_at) as day, SUM(amount) as total, COUNT(*) as cnt")
            ->groupBy('day')
            ->get()
            ->keyBy('day');   // keyed by "2026-03-07"

        $daysInMonth  = $start->daysInMonth;
        $firstWeekday = (int) $start->dayOfWeek; // 0=Sun, 1=Mon, … 6=Sat

        // Indonesian month names
        $monthNames = [
            1  => 'Januari', 2  => 'Februari', 3  => 'Maret',
            4  => 'April',   5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',    8  => 'Agustus',   9  => 'September',
            10 => 'Oktober', 11 => 'November',  12 => 'Desember',
        ];

        $days = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $iso  = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $row  = $rows->get($iso);
            $days[] = [
                'iso'       => $iso,
                'dateLabel' => $d . ' ' . $monthNames[$month] . ' ' . $year,
                'total'     => $row ? (int) $row->total : 0,
                'count'     => $row ? (int) $row->cnt   : 0,
            ];
        }

        return [
            'year'        => $year,
            'month'       => $month,
            'monthLabel'  => $monthNames[$month] . ' ' . $year,
            'firstWeekday'=> $firstWeekday,
            'daysInMonth' => $daysInMonth,
            'days'        => $days,
        ];
    }

    /**
     * Kirim test alert ke SSE stream tanpa menyimpan ke database donations
     */
    public function testAlert(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['ok' => false, 'error' => 'Profil streamer tidak ditemukan.'], 404);
        }

        // Nama & pesan random untuk test
        $names    = ['Budi Santoso', 'Siti Rahayu', 'Ahmad Fauzi', 'Dewi Pratiwi', 'Rizky Maulana'];
        $messages = ['Semangat streamnya!', 'GG streamer!', 'Keep it up!', 'Mantap kali bang!', 'Halo dari penonton setia!'];
        $emojis   = ['🎉', '💝', '🔥', '👏', '🌟'];
        $amounts  = [5000, 10000, 25000, 50000, 100000];

        $name    = $names[array_rand($names)];
        $message = $messages[array_rand($messages)];
        $emoji   = $emojis[array_rand($emojis)];
        $amount  = $amounts[array_rand($amounts)];

        try {
            // Generate seq dalam transaksi dengan lock untuk konsistensi
            DB::transaction(function () use ($streamer, $name, $message, $emoji, $amount) {
                $lastSeq = AlertQueue::where('streamer_id', $streamer->id)
                    ->lockForUpdate()
                    ->max('seq') ?? 0;
                $nextSeq = $lastSeq + 1;

                $payload = [
                    'id'        => 0,
                    'seq'       => $nextSeq,
                    'name'      => $name,
                    'amount'    => $amount,
                    'emoji'     => $emoji,
                    'msg'       => $message,
                    'ytUrl'     => null,
                    'ytEnabled' => false,
                    'duration'  => $streamer->alert_duration,
                    'time'      => now()->toIso8601String(),
                    'is_test'   => true,
                ];

                AlertQueue::create([
                    'streamer_id' => $streamer->id,
                    'donation_id' => null,
                    'seq'         => $nextSeq,
                    'payload'     => $payload,
                    'expires_at'  => now()->addMinutes(5),
                    'created_at'  => now(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('StreamerDashboardController: testAlert gagal', [
                'streamer_id' => $streamer->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'ok'    => false,
                'error' => 'Gagal mengirim test alert. Pastikan widget OBS sedang terhubung.',
            ], 500);
        }

        return response()->json([
            'ok'     => true,
            'name'   => $name,
            'amount' => $amount,
            'emoji'  => $emoji,
        ]);
    }
}
