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
            'slug'         => ['nullable', 'string', 'max:40', 'regex:/^[a-z0-9\-]+$/'],
            'bio'          => ['nullable', 'string', 'max:200'],
        ], [
            'slug.regex' => 'Slug hanya boleh huruf kecil, angka, dan tanda hubung (-).',
        ]);

        // Generate slug if not provided, or ensure provided slug is unique
        $slug = $this->resolveUniqueSlug(
            base: $validated['slug'] ?? '',
            fallbackName: $validated['display_name'],
        );

        Streamer::create([
            'user_id'      => $user->id,
            'slug'         => $slug,
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
     * Resolve a unique slug.
     *
     * - If $base is non-empty and not already taken → use it as-is.
     * - If $base is taken or empty → generate from $fallbackName + 4-char suffix,
     *   retrying up to 10 times until unique.
     */
    private function resolveUniqueSlug(string $base, string $fallbackName): string
    {
        $base = strtolower(trim($base));

        // Sanitize base: keep only a-z, 0-9, hyphens
        $base = preg_replace('/[^a-z0-9\-]/', '', $base);
        $base = preg_replace('/-+/', '-', $base);
        $base = trim($base, '-');

        // If valid and not taken, use it
        if ($base !== '' && !Streamer::where('slug', $base)->exists()) {
            return $base;
        }

        // Build slug from display name
        $nameBase = strtolower($fallbackName);
        $nameBase = preg_replace('/[^a-z0-9\s\-]/u', '', $nameBase);
        $nameBase = trim(preg_replace('/[\s]+/', '-', $nameBase), '-');
        $nameBase = preg_replace('/-+/', '-', $nameBase);
        $nameBase = substr($nameBase, 0, 30);

        if ($nameBase === '') {
            $nameBase = 'streamer';
        }

        // Try up to 10 times with different 4-char suffixes
        for ($i = 0; $i < 10; $i++) {
            $suffix    = Str::lower(Str::random(4));
            $candidate = $nameBase . '-' . $suffix;

            if (!Streamer::where('slug', $candidate)->exists()) {
                return $candidate;
            }
        }

        // Last resort: name + microsecond timestamp
        return $nameBase . '-' . substr((string) now()->timestamp, -6);
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
            'alert_theme'          => ['required', 'in:default,minimal,neon,fire,ice'],
            'yt_enabled'           => ['boolean'],
            'sound_enabled'        => ['boolean'],
            'notification_sound_preset' => ['nullable', 'string', 'in:ding,coin,whoosh,__custom__'],
            'avatar'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'sound_file'           => ['nullable', 'file', 'mimes:mp3,wav,ogg', 'max:5120'],
            'delete_sound'         => ['nullable', 'in:0,1'],
            'milestone_title'      => ['required', 'string', 'max:80'],
            'milestone_target'     => ['required', 'integer', 'min:1000'],
            'milestone_reset'      => ['boolean'],
            'leaderboard_title'    => ['required', 'string', 'max:80'],
            'leaderboard_count'    => ['required', 'integer', 'min:3', 'max:20'],
            'is_accepting_donation'=> ['boolean'],
            'thank_you_message'    => ['required', 'string', 'max:200'],
            'subathon_enabled'     => ['boolean'],
            'subathon_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ]);

        // ── Handle avatar upload (atomic: upload baru dulu, hapus lama hanya jika berhasil) ──
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            try {
                // Use guessExtension() for security - it checks MIME type, not user-provided extension
                $ext = $this->getSecureExtension($request->file('avatar'), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                
                if ($ext === null) {
                    return back()->withInput()->with('error', 'Tipe file avatar tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.');
                }
                
                // Secure filename: streamer_id + random string + extension
                $filename = $streamer->id . '_' . Str::random(8) . '.' . $ext;
                $newPath = $request->file('avatar')->storeAs('avatars', $filename, 'public');

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
                // Use guessExtension() for security - it checks MIME type, not user-provided extension
                $ext = $this->getSecureExtension($request->file('sound_file'), ['mp3', 'wav', 'ogg']);
                
                if ($ext === null) {
                    return back()->withInput()->with('error', 'Tipe file suara tidak didukung. Gunakan MP3, WAV, atau OGG.');
                }
                
                // Secure filename: streamer_id + random string + extension
                $filename = $streamer->id . '_' . Str::random(8) . '.' . $ext;
                $newPath = $request->file('sound_file')->storeAs('sounds', $filename, 'public');

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
            'subathon_enabled'      => $request->boolean('subathon_enabled'),
            'subathon_duration_minutes' => $validated['subathon_duration_minutes'] ?? 60,
        ]);

        // Handle subathon additional values (array)
        $subathonValues = $request->input('subathon_values', []);
        if (!empty($subathonValues)) {
            $values = [];
            foreach ($subathonValues as $v) {
                if (isset($v['from']) && isset($v['minutes'])) {
                    $values[] = [
                        'from' => (int) $v['from'],
                        'minutes' => (int) $v['minutes'],
                    ];
                }
            }
            if (!empty($values)) {
                usort($values, fn($a, $b) => $a['from'] <=> $b['from']);
                $streamer->subathon_additional_values = $values;
                $streamer->save();
            }
        }

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
        // WIB = UTC+7. Bulan WIB dimulai dari UTC prev-day 17:00 sampai UTC last-day 16:59.
        // Kurangi 7 jam dari start dan tambah 7 jam ke end agar whereBetween mencakup seluruh bulan WIB.
        $wibStart = \Carbon\Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta');
        $wibEnd   = $wibStart->copy()->endOfMonth();

        $start = $wibStart->copy()->setTimezone('UTC');
        $end   = $wibEnd->copy()->setTimezone('UTC');

        // Single aggregated query — date in WIB timezone (UTC+7)
        // SECURITY NOTE: Raw SQL used here is SAFE because:
        // - $driver is from DB::getDriverName() (controlled by config, not user input)
        // - All other query conditions use Eloquent's parameter binding
        // - The date expression contains no user-controllable variables
        // SQLite: datetime(created_at, '+7 hours')
        // MySQL:  CONVERT_TZ(created_at, 'UTC', 'Asia/Jakarta')
        $driver    = \Illuminate\Support\Facades\DB::getDriverName();
        $datExpr   = $driver === 'sqlite'
            ? "DATE(datetime(created_at, '+7 hours'))"
            : "DATE(CONVERT_TZ(created_at, 'UTC', 'Asia/Jakarta'))";

        $rows = \App\Models\Donation::where('streamer_id', $streamer->id)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw("{$datExpr} as day, SUM(amount) as total, COUNT(*) as cnt")
            ->groupBy('day')
            ->get()
            ->keyBy('day');   // keyed by "2026-03-07"

        $daysInMonth  = $wibStart->daysInMonth;
        $firstWeekday = (int) $wibStart->dayOfWeek; // 0=Sun, 1=Mon, … 6=Sat

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

    /**
     * Halaman Widget Studio
     */
    public function widgets(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->streamer) {
            return redirect()->route('streamer.setup');
        }

        $streamer        = $user->streamer;
        $widgetSettings  = $streamer->getWidgetSettings();
        $alertTiers      = $streamer->getAlertDurationTiers();
        $alertMaxDur     = min((int) ($streamer->alert_max_duration ?? 30), 120);
        $subathonValues  = $streamer->subathon_additional_values;

        return view('streamer.widgets', compact('streamer', 'widgetSettings', 'alertTiers', 'alertMaxDur', 'subathonValues'));
    }

    /**
     * Simpan widget settings (AJAX / form POST)
     */
    public function saveWidgets(Request $request): \Illuminate\Http\JsonResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['ok' => false, 'error' => 'Profil tidak ditemukan.'], 404);
        }

        $widget = $request->input('widget'); // 'alert','milestone','leaderboard','qr','subathon','running_text'
        $data   = $request->input('data', []);

        $allowed = ['alert', 'milestone', 'leaderboard', 'qr', 'subathon', 'running_text'];
        if (!in_array($widget, $allowed)) {
            return response()->json(['ok' => false, 'error' => 'Widget tidak dikenal.'], 422);
        }

        // Sanitize: hanya string keys, strip tags
        $clean = [];
        foreach ($data as $key => $val) {
            $clean[preg_replace('/[^a-z0-9_]/', '', strtolower($key))] = strip_tags((string) $val);
        }

        $current = $streamer->widget_settings ?? [];
        $current[$widget] = $clean;
        $streamer->widget_settings = $current;
        $streamer->save();

        ActivityLog::log(
            action: 'streamer.widgets.saved',
            description: "Widget '{$widget}' settings disimpan",
            streamerId: $streamer->id,
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Simpan pengaturan alert: audio, durasi tier, youtube/video toggle (AJAX).
     * POST /streamer/widgets/alert-settings
     */
    public function saveAlertSettings(Request $request): \Illuminate\Http\JsonResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['ok' => false, 'error' => 'Profil tidak ditemukan.'], 404);
        }

        $validSounds = [
            'ding', 'coin', 'whoosh', 'chime', 'pop', 'tada',
            'woosh_light', 'blip', 'sparkle', 'fanfare',
        ];

        $validated = $request->validate([
            'sound_enabled'        => ['boolean'],
            'notification_sound'   => ['nullable', 'string', 'in:' . implode(',', $validSounds)],
            'yt_enabled'           => ['boolean'],
            'alert_max_duration'   => ['required', 'integer', 'min:5', 'max:120'],
            'alert_duration_tiers' => ['required', 'array', 'min:1', 'max:4'],
            'alert_duration_tiers.*.from'     => ['required', 'integer', 'min:0'],
            'alert_duration_tiers.*.duration' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        // Enforce max_duration cap on each tier
        $maxDur = (int) $validated['alert_max_duration'];
        $tiers  = array_map(function ($tier) use ($maxDur) {
            return [
                'from'     => (int) $tier['from'],
                'duration' => min((int) $tier['duration'], $maxDur),
            ];
        }, $validated['alert_duration_tiers']);

        // Sort tiers ascending by `from`
        usort($tiers, fn($a, $b) => $a['from'] <=> $b['from']);

        $streamer->fill([
            'sound_enabled'        => $request->boolean('sound_enabled'),
            'notification_sound'   => $validated['notification_sound'] ?? $streamer->notification_sound,
            'yt_enabled'           => $request->boolean('yt_enabled'),
            'alert_max_duration'   => $maxDur,
            'alert_duration_tiers' => $tiers,
        ])->save();

        ActivityLog::log(
            action: 'streamer.alert-settings.saved',
            description: 'Alert settings (audio, durasi tier, video) disimpan',
            streamerId: $streamer->id,
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Halaman Subathon Settings - redirect ke Widget Studio tab Subathon
     */
    public function subathon(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->streamer) {
            return redirect()->route('streamer.setup');
        }

        return redirect()->route('streamer.widgets', ['#' => 'subathon']);
    }

    /**
     * Simpan pengaturan Subathon (AJAX)
     */
    public function saveSubathonSettings(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['ok' => false, 'error' => 'Profil tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'subathon_enabled'           => ['boolean'],
            'subathon_duration_minutes'  => ['required', 'integer', 'min:1', 'max:1440'],
            'subathon_additional_values' => ['required', 'array', 'min:1'],
            'subathon_additional_values.*.from'     => ['required', 'integer', 'min:0'],
            'subathon_additional_values.*.minutes'   => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $values = array_map(function ($v) {
            return [
                'from'    => (int) $v['from'],
                'minutes' => (int) $v['minutes'],
            ];
        }, $validated['subathon_additional_values']);

        usort($values, fn($a, $b) => $a['from'] <=> $b['from']);

        $streamer->fill([
            'subathon_enabled'           => $request->boolean('subathon_enabled'),
            'subathon_duration_minutes'  => $validated['subathon_duration_minutes'],
            'subathon_additional_values'  => $values,
        ])->save();

        ActivityLog::log(
            action: 'streamer.subathon.saved',
            description: 'Pengaturan Subathon disimpan',
            streamerId: $streamer->id,
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Reset timer Subathon (AJAX)
     */
    public function resetSubathonTimer(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['ok' => false, 'error' => 'Profil tidak ditemukan.'], 404);
        }

        $streamer->resetSubathonTimer();

        ActivityLog::log(
            action: 'streamer.subathon.timer.reset',
            description: 'Timer Subathon di-reset ke default',
            streamerId: $streamer->id,
        );

        return response()->json([
            'ok'    => true,
            'timer' => $streamer->subathon_current_minutes,
            'formatted' => $streamer->subathon_timer_formatted,
        ]);
    }

    /**
     * Tambah waktu Subathon secara manual (AJAX)
     */
    public function addSubathonTimeManual(Request $request): JsonResponse
    {
        $user     = Auth::user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['ok' => false, 'error' => 'Profil tidak ditemukan.'], 404);
        }

        $minutes = $request->input('minutes', 0);

        if ($minutes < 1 || $minutes > 60) {
            return response()->json(['ok' => false, 'error' => 'Menit harus antara 1-60.'], 422);
        }

        $streamer->subathon_current_minutes = ($streamer->subathon_current_minutes ?? 0) + $minutes;
        $streamer->subathon_last_updated = now();
        $streamer->save();

        ActivityLog::log(
            action: 'streamer.subathon.timer.add',
            description: "Timer Subathon ditambah {$minutes} menit",
            streamerId: $streamer->id,
        );

        return response()->json([
            'ok'    => true,
            'timer' => $streamer->subathon_current_minutes,
            'formatted' => $streamer->subathon_timer_formatted,
        ]);
    }

    /**
     * Get secure file extension by checking MIME type instead of user-provided extension.
     * Returns null if the file type is not in the allowed list.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $allowedExtensions List of allowed extensions (e.g., ['jpg', 'png', 'gif'])
     * @return string|null The secure extension, or null if not allowed
     */
    private function getSecureExtension($file, array $allowedExtensions): ?string
    {
        // Use guessExtension() which checks MIME type via finfo, not user input
        $guessedExt = strtolower($file->guessExtension() ?? '');
        
        // Normalize jpeg to jpg for consistency
        if ($guessedExt === 'jpeg') {
            $guessedExt = 'jpg';
        }
        
        // Map MIME-based extension variations to standard ones
        $extensionMap = [
            'mpga' => 'mp3',  // audio/mpeg sometimes returns 'mpga'
            'oga'  => 'ogg',  // audio/ogg sometimes returns 'oga'
        ];
        
        if (isset($extensionMap[$guessedExt])) {
            $guessedExt = $extensionMap[$guessedExt];
        }
        
        // Check if guessed extension is in allowed list
        if (in_array($guessedExt, $allowedExtensions, true)) {
            return $guessedExt;
        }
        
        // Fallback: Also check client extension but only if it matches MIME detection
        // This handles edge cases where MIME detection might fail
        $clientExt = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
        
        if ($clientExt === 'jpeg') {
            $clientExt = 'jpg';
        }
        
        // Only allow client extension if both:
        // 1. It's in the allowed list
        // 2. The MIME type is valid for that file type (extra validation)
        if (in_array($clientExt, $allowedExtensions, true)) {
            $mime = strtolower($file->getMimeType() ?? '');
            
            $validMimeMap = [
                'jpg'  => ['image/jpeg'],
                'jpeg' => ['image/jpeg'],
                'png'  => ['image/png'],
                'gif'  => ['image/gif'],
                'webp' => ['image/webp'],
                'mp3'  => ['audio/mpeg', 'audio/mp3'],
                'wav'  => ['audio/wav', 'audio/x-wav', 'audio/wave'],
                'ogg'  => ['audio/ogg', 'application/ogg'],
            ];
            
            if (isset($validMimeMap[$clientExt]) && in_array($mime, $validMimeMap[$clientExt], true)) {
                return $clientExt;
            }
        }
        
        return null;
    }
}
