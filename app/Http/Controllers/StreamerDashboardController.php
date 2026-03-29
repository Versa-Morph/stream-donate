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
        $user = auth()->user();

        // Jika belum punya profil streamer, redirect ke setup
        if (!$user->streamer) {
            return redirect()->route('streamer.setup');
        }

        $streamer  = $user->streamer;
        $stats     = $streamer->buildStats();

        // Donasi terbaru (50 terakhir)
        $donations = $streamer->donations()
            ->orderBy('created_at', 'desc')
            ->limit(config('pagination.dashboard_donations', 50))
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
        $user = auth()->user();

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
        $user = auth()->user();

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
     * Resolve a unique slug for a streamer profile.
     *
     * Strategy:
     * - If $base is non-empty and not already taken → use it as-is.
     * - If $base is taken or empty → generate from $fallbackName + 4-char suffix,
     *   retrying up to 10 times until unique.
     *
     * @param string $base The preferred slug (may be empty or already taken)
     * @param string $fallbackName Display name to use for slug generation if base unavailable
     * @return string A unique slug guaranteed not to exist in database
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
        $user = auth()->user();

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
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return redirect()->route('streamer.setup');
        }

        $validated = $request->validate([
            'display_name'         => ['required', 'string', 'max:60'],
            'bio'                  => ['nullable', 'string', 'max:200'],
            'min_donation'         => ['required', 'integer', 'min:100', 'max:1000000'],
            'avatar'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'is_accepting_donation'=> ['boolean'],
            'thank_you_message'    => ['required', 'string', 'max:200'],
            // Media upload settings
            'media_upload_enabled' => ['boolean'],
            'media_max_size_mb'    => ['nullable', 'integer', 'min:1', 'max:500'],
            'media_tiers'          => ['nullable', 'array', 'max:10'],
            'media_tiers.*.min_amount'   => ['nullable', 'integer', 'min:1000'],
            'media_tiers.*.max_duration' => ['nullable', 'integer', 'min:5', 'max:600'],
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


        $streamer->update([
            'display_name'          => $validated['display_name'],
            'bio'                   => $validated['bio'] ?? null,
            'min_donation'          => $validated['min_donation'],
            'is_accepting_donation' => $request->boolean('is_accepting_donation'),
            'thank_you_message'     => $validated['thank_you_message'],
            'media_upload_enabled'  => $request->boolean('media_upload_enabled'),
            'media_max_size_mb'     => $validated['media_max_size_mb'] ?? 50,
        ]);

        // Handle media duration tiers (array)
        $mediaTiers = $request->input('media_tiers', []);
        if (!empty($mediaTiers)) {
            $tiers = [];
            foreach ($mediaTiers as $t) {
                if (isset($t['min_amount']) && isset($t['max_duration']) && 
                    (int) $t['min_amount'] > 0 && (int) $t['max_duration'] > 0) {
                    $tiers[] = [
                        'min_amount' => (int) $t['min_amount'],
                        'max_duration' => (int) $t['max_duration'],
                    ];
                }
            }
            if (!empty($tiers)) {
                // Sort by min_amount ascending
                usort($tiers, fn($a, $b) => $a['min_amount'] <=> $b['min_amount']);
                $streamer->media_duration_tiers = $tiers;
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
        $user     = auth()->user();
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
     * AJAX: kembalikan data heatmap untuk bulan tertentu (JSON).
     *
     * GET /streamer/heatmap-data?year=2026&month=3
     * Validates year/month parameters and falls back to current date if invalid.
     *
     * @param Request $request The HTTP request with optional year/month query params
     * @return JsonResponse Heatmap data array
     */
    public function heatmapData(Request $request): JsonResponse
    {
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Streamer tidak ditemukan.'], 404);
        }

        // Validate with fallback to current date (user-friendly for AJAX)
        $validator = \Illuminate\Support\Facades\Validator::make($request->query(), [
            'year'  => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            // Fall back to current date instead of returning error (better UX for AJAX)
            $year  = now()->year;
            $month = now()->month;
        } else {
            $validated = $validator->validated();
            $year  = $validated['year'] ?? now()->year;
            $month = $validated['month'] ?? now()->month;
        }

        return response()->json($this->buildMonthHeatmap($streamer, $year, $month));
    }

    /**
     * Build heatmap data for a given month.
     *
     * Aggregates donation data by day (in WIB timezone) for calendar heatmap visualization.
     * Uses timezone-aware SQL to properly group donations occurring in Indonesian time.
     *
     * @param \App\Models\Streamer $streamer The streamer to build heatmap for
     * @param int $year Year (e.g., 2026)
     * @param int $month Month (1-12)
     * @return array Heatmap structure with keys:
     *   - year: int
     *   - month: int
     *   - monthLabel: string (e.g., "Maret 2026")
     *   - firstWeekday: int (0=Sun, 6=Sat - day of week for 1st of month)
     *   - daysInMonth: int
     *   - days: array of [{iso, dateLabel, total, count}, ...] (1 entry per day)
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
     * Kirim test alert ke SSE stream tanpa menyimpan ke database donations.
     *
     * Includes database-level rate limiting in addition to middleware throttle.
     *
     * @param Request $request The HTTP request
     * @return JsonResponse Success/failure response with test alert data
     */
    public function testAlert(Request $request): JsonResponse
    {
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Profil streamer tidak ditemukan.'], 404);
        }

        // Database-level rate limiting (additional protection beyond middleware)
        $testAlertConfig = config('alert.test_alert');
        $recentTestAlerts = AlertQueue::where('streamer_id', $streamer->id)
            ->whereNull('donation_id') // Test alerts have null donation_id
            ->where('created_at', '>=', now()->subMinutes($testAlertConfig['window_minutes']))
            ->count();

        if ($recentTestAlerts >= $testAlertConfig['max_per_window']) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak test alert. Tunggu beberapa menit.',
            ], 429);
        }

        // Nama & pesan random untuk test dari config
        $testConfig = config('donation.test_alert');
        $names    = $testConfig['names'];
        $messages = $testConfig['messages'];
        $emojis   = $testConfig['emojis'];
        $amounts  = $testConfig['amounts'];

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
                'success' => false,
                'message' => 'Gagal mengirim test alert. Pastikan widget OBS sedang terhubung.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test alert berhasil dikirim.',
            'data'    => [
                'name'   => $name,
                'amount' => $amount,
                'emoji'  => $emoji,
            ],
        ]);
    }

    /**
     * Halaman Widget Studio
     */
    public function widgets(): View|RedirectResponse
    {
        $user = auth()->user();

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
     * Simpan widget settings (AJAX / form POST).
     *
     * Validates widget type and settings data before saving.
     *
     * @param Request $request The HTTP request containing widget and data
     * @return \Illuminate\Http\JsonResponse Success/failure response
     */
    public function saveWidgets(Request $request): \Illuminate\Http\JsonResponse
    {
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        }

        // Validate request structure
        $allowed = ['alert', 'milestone', 'leaderboard', 'qr', 'subathon', 'running_text'];

        try {
            $validated = $request->validate([
                'widget' => ['required', 'string', 'in:' . implode(',', $allowed)],
                'data'   => ['required', 'array', 'max:50'], // Max 50 settings per widget
                'data.*' => ['nullable', 'string', 'max:' . config('alert.widget_validation.max_string_length', 255)],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        }

        $widget = $validated['widget'];
        $data   = $validated['data'];

        // Sanitize: normalize keys, strip HTML tags, validate specific field types
        $clean = [];
        $validation = config('alert.widget_validation');

        foreach ($data as $key => $val) {
            $normalizedKey = preg_replace('/[^a-z0-9_]/', '', strtolower($key));
            if (empty($normalizedKey)) {
                continue; // Skip invalid keys
            }

            $sanitizedVal = strip_tags((string) $val);

            // Additional validation for specific field patterns
            if (str_contains($normalizedKey, 'color') && !empty($sanitizedVal)) {
                // Validate color format (hex)
                if (!preg_match($validation['allowed_color_pattern'], $sanitizedVal)) {
                    $sanitizedVal = ''; // Invalid color, reset to empty
                }
            }

            if (str_contains($normalizedKey, 'duration') || str_contains($normalizedKey, 'size') || str_contains($normalizedKey, 'width') || str_contains($normalizedKey, 'height')) {
                // Ensure numeric fields are within bounds
                if (is_numeric($sanitizedVal)) {
                    $sanitizedVal = (string) min((int) $sanitizedVal, $validation['max_numeric_value']);
                }
            }

            $clean[$normalizedKey] = $sanitizedVal;
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

        return response()->json(['success' => true, 'message' => 'Widget settings berhasil disimpan.']);
    }

    /**
     * Simpan pengaturan alert: audio, durasi tier, youtube/video toggle (AJAX).
     * POST /streamer/widgets/alert-settings
     */
    public function saveAlertSettings(Request $request): \Illuminate\Http\JsonResponse
    {
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        }

        $validSounds = config('alert.sound_presets', [
            'ding', 'coin', 'whoosh', 'chime', 'pop', 'tada',
            'woosh_light', 'blip', 'sparkle', 'fanfare',
        ]);

        $validated = $request->validate([
            'sound_enabled'        => ['boolean'],
            'notification_sound'   => ['nullable', 'string', 'in:' . implode(',', $validSounds)],
            // Media channels
            'yt_enabled'           => ['boolean'],
            'tiktok_enabled'      => ['boolean'],
            'instagram_enabled'   => ['boolean'],
            'twitter_enabled'     => ['boolean'],
            'spotify_enabled'     => ['boolean'],
            'media_upload_enabled' => ['boolean'],
            'media_max_size_mb'   => ['nullable', 'integer', 'min:1', 'max:500'],
            'media_tiers'         => ['nullable', 'array', 'max:10'],
            'media_tiers.*.min_amount'   => ['nullable', 'integer', 'min:1000'],
            'media_tiers.*.max_duration' => ['nullable', 'integer', 'min:5', 'max:600'],
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

        // Process media tiers
        $mediaTiers = [];
        if (!empty($validated['media_tiers'])) {
            $mediaTiers = array_filter($validated['media_tiers'], function($tier) {
                return !empty($tier['min_amount']) && !empty($tier['max_duration']);
            });
            $mediaTiers = array_values($mediaTiers);
        }

        $streamer->fill([
            'sound_enabled'        => $request->boolean('sound_enabled'),
            'notification_sound'   => $validated['notification_sound'] ?? $streamer->notification_sound,
            // Media channels
            'yt_enabled'           => $request->boolean('yt_enabled'),
            'tiktok_enabled'      => $request->boolean('tiktok_enabled'),
            'instagram_enabled'   => $request->boolean('instagram_enabled'),
            'twitter_enabled'     => $request->boolean('twitter_enabled'),
            'spotify_enabled'     => $request->boolean('spotify_enabled'),
            'media_upload_enabled' => $request->boolean('media_upload_enabled'),
            'media_max_size_mb'   => $validated['media_max_size_mb'] ?? $streamer->media_max_size_mb ?? 50,
            'media_duration_tiers' => !empty($mediaTiers) ? $mediaTiers : $streamer->media_duration_tiers,
            'alert_max_duration'   => $maxDur,
            'alert_duration_tiers' => $tiers,
        ])->save();

        ActivityLog::log(
            action: 'streamer.alert-settings.saved',
            description: 'Alert settings (audio, durasi tier, video) disimpan',
            streamerId: $streamer->id,
        );

        return response()->json(['success' => true, 'message' => 'Alert settings berhasil disimpan.']);
    }

    /**
     * Simpan pengaturan Milestone Widget (AJAX).
     * POST /streamer/widgets/milestone-settings
     */
    public function saveMilestoneSettings(Request $request): JsonResponse
    {
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'milestone_title'  => ['required', 'string', 'max:80'],
            'milestone_target' => ['required', 'integer', 'min:1000', 'max:1000000000'],
            'milestone_reset'  => ['boolean'],
        ]);

        $streamer->fill([
            'milestone_title'  => $validated['milestone_title'],
            'milestone_target' => $validated['milestone_target'],
            'milestone_reset'  => $request->boolean('milestone_reset'),
        ])->save();

        ActivityLog::log(
            action: 'streamer.milestone-settings.saved',
            description: 'Pengaturan Milestone disimpan',
            streamerId: $streamer->id,
        );

        return response()->json(['success' => true, 'message' => 'Pengaturan Milestone berhasil disimpan.']);
    }

    /**
     * Simpan pengaturan Leaderboard Widget (AJAX).
     * POST /streamer/widgets/leaderboard-settings
     */
    public function saveLeaderboardSettings(Request $request): JsonResponse
    {
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'leaderboard_title' => ['required', 'string', 'max:80'],
            'leaderboard_count' => ['required', 'integer', 'min:3', 'max:20'],
        ]);

        $streamer->fill([
            'leaderboard_title' => $validated['leaderboard_title'],
            'leaderboard_count' => $validated['leaderboard_count'],
        ])->save();

        ActivityLog::log(
            action: 'streamer.leaderboard-settings.saved',
            description: 'Pengaturan Leaderboard disimpan',
            streamerId: $streamer->id,
        );

        return response()->json(['success' => true, 'message' => 'Pengaturan Leaderboard berhasil disimpan.']);
    }

    /**
     * Halaman Subathon Settings - redirect ke Widget Studio tab Subathon
     */
    public function subathon(): RedirectResponse
    {
        $user = auth()->user();

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
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
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

        return response()->json(['success' => true, 'message' => 'Pengaturan Subathon berhasil disimpan.']);
    }

    /**
     * Reset timer Subathon (AJAX)
     */
    public function resetSubathonTimer(Request $request): JsonResponse
    {
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        }

        $streamer->resetSubathonTimer();

        ActivityLog::log(
            action: 'streamer.subathon.timer.reset',
            description: 'Timer Subathon di-reset ke default',
            streamerId: $streamer->id,
        );

        return response()->json([
            'success'   => true,
            'message'   => 'Timer Subathon berhasil di-reset.',
            'data'      => [
                'timer'     => $streamer->subathon_current_minutes,
                'formatted' => $streamer->subathon_timer_formatted,
            ],
        ]);
    }

    /**
     * Tambah waktu Subathon secara manual (AJAX)
     */
    public function addSubathonTimeManual(Request $request): JsonResponse
    {
        $user     = auth()->user();
        $streamer = $user->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan.'], 404);
        }

        $minutes = $request->input('minutes', 0);

        if ($minutes < 1 || $minutes > 60) {
            return response()->json(['success' => false, 'message' => 'Menit harus antara 1-60.'], 422);
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
            'success'   => true,
            'message'   => "Timer Subathon berhasil ditambah {$minutes} menit.",
            'data'      => [
                'timer'     => $streamer->subathon_current_minutes,
                'formatted' => $streamer->subathon_timer_formatted,
            ],
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
