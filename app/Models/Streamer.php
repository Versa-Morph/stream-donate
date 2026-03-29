<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Streamer extends Model
{
    use HasFactory;

    /**
     * Attributes that should be mass assignable.
     * Note: api_key is NOT in fillable to prevent mass assignment attacks.
     */
    protected $fillable = [
        'user_id',
        'slug',
        'display_name',
        'bio',
        'avatar',
        'alert_duration',
        'yt_enabled',
        'sound_enabled',
        'notification_sound',
        'alert_theme',
        'milestone_title',
        'milestone_target',
        'milestone_reset',
        'leaderboard_title',
        'leaderboard_count',
        'min_donation',
        'is_accepting_donation',
        'thank_you_message',
        'canvas_config',
        'widget_settings',
        'alert_duration_tiers',
        'alert_max_duration',
        'subathon_enabled',
        'subathon_duration_minutes',
        'subathon_additional_values',
        'subathon_current_minutes',
        'subathon_last_updated',
        'media_duration_tiers',
        'media_upload_enabled',
        'media_max_size_mb',
        // Media channels
        'tiktok_enabled',
        'instagram_enabled',
        'twitter_enabled',
        'spotify_enabled',
    ];

    /**
     * Attributes that should be hidden from arrays/JSON.
     * Critical: API key must never be exposed in responses.
     */
    protected $hidden = [
        'api_key',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'yt_enabled' => 'boolean',
            'sound_enabled' => 'boolean',
            'milestone_reset' => 'boolean',
            'is_accepting_donation' => 'boolean',
            'milestone_target' => 'integer',
            'alert_duration' => 'integer',
            'leaderboard_count' => 'integer',
            'min_donation' => 'integer',
            'canvas_config'          => 'array',
            'widget_settings'        => 'array',
            'alert_duration_tiers'   => 'array',
            'alert_max_duration'     => 'integer',
            'subathon_enabled'       => 'boolean',
            'subathon_duration_minutes' => 'integer',
            'subathon_additional_values' => 'array',
            'subathon_current_minutes' => 'integer',
            'subathon_last_updated'  => 'datetime',
            'media_duration_tiers'   => 'array',
            'media_upload_enabled'   => 'boolean',
            'media_max_size_mb'      => 'integer',
        ];
    }

    /**
     * Generate a unique API key for streamer authentication.
     *
     * @return string 40 random characters + CRC32B hash of microtime
     */
    public static function generateApiKey(): string
    {
        return Str::random(40) . hash('crc32b', microtime());
    }

    /**
     * Get the user that owns the streamer profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all donations for this streamer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get all alert queue items for this streamer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function alertQueues(): HasMany
    {
        return $this->hasMany(AlertQueue::class);
    }

    /**
     * Get all activity logs for this streamer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get all milestones for this streamer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    /**
     * Get active milestones ordered by order field.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeMilestones(): HasMany
    {
        return $this->hasMany(Milestone::class)
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Get default media duration tiers.
     *
     * @return array Default tiers for media upload duration
     */
    public static function getDefaultMediaDurationTiers(): array
    {
        return [
            ['min_amount' => 10000, 'max_duration' => 15],
            ['min_amount' => 25000, 'max_duration' => 30],
            ['min_amount' => 50000, 'max_duration' => 60],
            ['min_amount' => 100000, 'max_duration' => 120],
            ['min_amount' => 250000, 'max_duration' => 180],
        ];
    }

    /**
     * Get media duration tiers with defaults.
     *
     * @return array Media duration tiers
     */
    public function getMediaDurationTiers(): array
    {
        return $this->media_duration_tiers ?? self::getDefaultMediaDurationTiers();
    }

    /**
     * Get maximum allowed media duration based on donation amount.
     *
     * @param int $amount Donation amount in rupiah
     * @return int Maximum duration in seconds, 0 if not allowed
     */
    public function getMaxMediaDuration(int $amount): int
    {
        if (!$this->media_upload_enabled) {
            return 0;
        }

        $tiers = $this->getMediaDurationTiers();
        $maxDuration = 0;

        // Sort tiers by min_amount ascending
        usort($tiers, fn($a, $b) => $a['min_amount'] <=> $b['min_amount']);

        foreach ($tiers as $tier) {
            if ($amount >= $tier['min_amount']) {
                $maxDuration = $tier['max_duration'];
            }
        }

        return $maxDuration;
    }

    /**
     * Check if donation amount allows media upload.
     *
     * @param int $amount Donation amount in rupiah
     * @return bool True if media upload is allowed
     */
    public function canUploadMedia(int $amount): bool
    {
        return $this->getMaxMediaDuration($amount) > 0;
    }

    /**
     * Kembalikan widget_settings dengan nilai default jika null/kosong.
     *
     * @return array Widget settings merged with defaults
     */
    public function getWidgetSettings(): array
    {
        $defaults = [
            'alert' => [
                'preset'          => 'default',
                'bg'              => 'rgba(8,8,12,0.96)',
                'border'          => 'rgba(255,255,255,0.1)',
                'accent'          => '#7c6cfc',
                'accent2'         => '#a99dff',
                'amount_color'    => '#f97316',
                'donor_color'     => '#f1f1f6',
                'top_line'        => 'linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0)',
                'prog_bar'        => 'linear-gradient(90deg,#7c6cfc,#f97316)',
                'radius'          => '16',
                'width'           => '560',
                'position_x'      => 'center',
                'position_y'      => 'bottom',
                'layout'          => 'classic',
                'style'           => 'glass',
                'font_family'     => 'inter',
                'font_size_title' => '17',
                'font_size_amount'=> '24',
                'font_size_msg'   => '13',
                'spacing'         => '2',
                'blur_amount'     => '12',
            ],
            'milestone' => [
                'preset'       => 'default',
                'surface'      => 'rgba(8,8,12,0.96)',
                'border'       => 'rgba(124,108,252,0.2)',
                'brand'        => '#7c6cfc',
                'brand2'       => '#a99dff',
                'orange'       => '#f97316',
                'green'        => '#22d3a0',
                'radius'       => '16',
                'width'        => '340',
                'position'     => 'bottom-left',
            ],
            'leaderboard' => [
                'preset'       => 'default',
                'surface'      => 'rgba(8,8,12,0.96)',
                'border'       => 'rgba(124,108,252,0.2)',
                'brand'        => '#7c6cfc',
                'brand2'       => '#a99dff',
                'yellow'       => '#fbbf24',
                'green'        => '#22d3a0',
                'radius'       => '16',
                'width'        => '300',
                'position'     => 'top-left',
            ],
            'qr' => [
                'preset'       => 'default',
                'surface'      => 'rgba(10,10,16,0.93)',
                'border'       => 'rgba(124,108,252,0.28)',
                'brand'        => '#7c6cfc',
                'brand2'       => '#a99dff',
                'radius'       => '22',
                'width'        => '260',
                'position'     => 'bottom-right',
            ],
            'subathon' => [
                'preset'       => 'default',
                'bg'           => 'rgba(8,8,12,0.95)',
                'border'       => 'rgba(124,108,252,0.25)',
                'brand'        => '#7c6cfc',
                'brand2'       => '#a99dff',
                'text'         => '#f1f1f6',
                'text2'        => '#a0a0b4',
                'radius'       => '16',
                'width'        => '320',
                'showLabel'    => true,
                'labelText'    => 'Sisa Waktu',
            ],
            'running_text' => [
                'preset'       => 'default',
                'enabled'      => false,
                'text'         => 'Terima kasih atas donasi Anda! Semangat terus streamnya!',
                'speed'        => '50',
                'direction'    => 'left',
                'bg'           => 'rgba(8,8,12,0.9)',
                'border'       => 'rgba(124,108,252,0.2)',
                'brand'        => '#7c6cfc',
                'text_color'   => '#ffffff',
                'font_size'    => '18',
                'font_family'  => 'inter',
                'radius'       => '0',
                'opacity'      => '90',
            ],
        ];

        $saved = $this->widget_settings;
        if (empty($saved)) return $defaults;

        foreach ($defaults as $widget => $def) {
            if (!isset($saved[$widget])) {
                $saved[$widget] = $def;
            } else {
                $saved[$widget] = array_merge($def, $saved[$widget]);
            }
        }

        return $saved;
    }

    /**
     * Kembalikan canvas_config dengan nilai default jika null/kosong.
     *
     * @return array Canvas configuration merged with defaults
     */
    public function getCanvasConfig(): array
    {
        $defaults = [
            'width'  => 1920,
            'height' => 1080,
            'widgets' => [
                'notification' => ['active' => true,  'x' => 680,  'y' => 820, 'w' => 560, 'h' => 200],
                'leaderboard'  => ['active' => false, 'x' => 60,   'y' => 60,  'w' => 300, 'h' => 420],
                'milestone'    => ['active' => false, 'x' => 40,   'y' => 800, 'w' => 340, 'h' => 130],
                'qrcode'       => ['active' => false, 'x' => 1620, 'y' => 760, 'w' => 260, 'h' => 300],
                'subathon'     => ['active' => false, 'x' => 800,  'y' => 100, 'w' => 320, 'h' => 150],
                'running_text' => ['active' => false, 'x' => 0,    'y' => 1040, 'w' => 1920, 'h' => 40],
            ],
        ];

        $saved = $this->canvas_config;
        if (empty($saved)) return $defaults;

        // Merge widget-level defaults agar key baru tidak hilang
        foreach ($defaults['widgets'] as $key => $def) {
            if (!isset($saved['widgets'][$key])) {
                $saved['widgets'][$key] = $def;
            } else {
                $saved['widgets'][$key] = array_merge($def, $saved['widgets'][$key]);
            }
        }

        return array_merge($defaults, $saved);
    }

    /**
     * Kembalikan alert_duration_tiers dengan nilai default jika null/kosong.
     *
     * Tier default (dari kecil ke besar berdasarkan `from`):
     *   0        Rp → 5 detik
     *   1.000    Rp → 8 detik
     *   10.000   Rp → 12 detik
     *   100.000  Rp → 20 detik
     */
    public function getAlertDurationTiers(): array
    {
        $defaults = config('alert.default_tiers', [
            ['from' => 0,       'duration' => 5],
            ['from' => 10000,   'duration' => 8],
            ['from' => 50000,   'duration' => 12],
            ['from' => 100000,  'duration' => 20],
        ]);

        return $this->alert_duration_tiers ?? $defaults;
    }

    /**
     * Hitung durasi alert (dalam milidetik) berdasarkan jumlah donasi dan tier yang aktif.
     * Kembalikan durasi tier tertinggi yang `from`-nya <= amount.
     */
    public function getAlertDurationForAmount(int $amount): int
    {
        $tiers   = $this->getAlertDurationTiers();
        $maxSecs = min((int) ($this->alert_max_duration ?? 30), 120); // system cap 120 s

        // Sort descending by `from` dan ambil tier pertama yang cocok
        usort($tiers, fn($a, $b) => $b['from'] <=> $a['from']);
        foreach ($tiers as $tier) {
            if ($amount >= (int) $tier['from']) {
                $secs = min((int) $tier['duration'], $maxSecs);
                return $secs * 1000; // ms
            }
        }

        // Fallback ke legacy alert_duration
        return (int) ($this->alert_duration ?? 8000);
    }

    /**
     * Get subathon additional values with default fallback.
     *
     * Laravel accessor that returns tier-based minute additions for donations.
     * Accessible as $streamer->subathon_additional_values
     *
     * @param mixed $value Raw database value (JSON string or array)
     * @return array Array of tiers with 'from' (amount) and 'minutes' (added time) keys
     *               Example: [['from' => 0, 'minutes' => 1], ['from' => 10000, 'minutes' => 2]]
     */
    public function getSubathonAdditionalValuesAttribute($value): array
    {
        $defaults = config('alert.subathon.default_additional_values', [
            ['from' => 0, 'minutes' => 1],
            ['from' => 10000, 'minutes' => 2],
            ['from' => 50000, 'minutes' => 5],
            ['from' => 100000, 'minutes' => 10],
        ]);

        if (empty($value)) return $defaults;

        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (empty($decoded)) return $defaults;

        return $decoded;
    }

    /**
     * Hitung tambahan menit berdasarkan jumlah donasi.
     * Kembalikan menit tambahan dari tier tertinggi yang `from` <= amount.
     *
     * @param int $amount Donation amount in rupiah
     * @return int Minutes to add to subathon timer
     */
    public function getSubathonMinutesForAmount(int $amount): int
    {
        $values = $this->subathon_additional_values;
        usort($values, fn($a, $b) => $b['from'] <=> $a['from']);

        foreach ($values as $v) {
            if ($amount >= (int) $v['from']) {
                return (int) $v['minutes'];
            }
        }

        return 0;
    }

    /**
     * Format timer remaining dalam format HH:MM:SS.
     *
     * Laravel accessor that formats subathon_current_minutes as HH:MM:SS string.
     * This attribute can be accessed as $streamer->subathon_timer_formatted
     *
     * @return string Formatted time string (e.g., "02:30:00" for 150 minutes)
     */
    public function getSubathonTimerFormattedAttribute(): string
    {
        $minutes = $this->subathon_current_minutes ?? 0;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d:00', $hours, $mins);
    }

    /**
     * Reset timer ke durasi default.
     *
     * @return void
     */
    public function resetSubathonTimer(): void
    {
        $this->subathon_current_minutes = $this->subathon_duration_minutes ?? 60;
        $this->subathon_last_updated = now();
        $this->save();
    }

    /**
     * Tambah waktu ke timer berdasarkan donasi.
     * FIXED: Use atomic increment to prevent race conditions.
     *
     * @param int $donationAmount Donation amount in rupiah
     * @return array{added: int, new_total: int} Minutes added and new total
     */
    public function addSubathonTime(int $donationAmount): array
    {
        if (!$this->subathon_enabled) {
            return ['added' => 0, 'new_total' => $this->subathon_current_minutes];
        }

        $addedMinutes = $this->getSubathonMinutesForAmount($donationAmount);

        if ($addedMinutes > 0) {
            // SECURITY FIX: Atomic increment prevents race condition when multiple donations arrive simultaneously
            $this->increment('subathon_current_minutes', $addedMinutes);
            $this->subathon_last_updated = now();
            $this->save();
            
            // Refresh to get updated value after increment
            $this->refresh();
        }

        return [
            'added' => $addedMinutes,
            'new_total' => $this->subathon_current_minutes,
        ];
    }

    /**
     * Total donasi semua waktu.
     *
     * @return int Total donations in rupiah
     */
    public function getTotalDonationsAttribute(): int
    {
        return $this->donations()->sum('amount');
    }

    /**
     * Total donasi hari ini.
     *
     * @return int Today's donations in rupiah
     */
    public function getTodayDonationsAttribute(): int
    {
        return $this->donations()->whereDate('created_at', today())->sum('amount');
    }

    /**
     * Statistik lengkap untuk dashboard & SSE.
     *
     * PERFORMANCE FIX: Cached with dynamic TTL based on streamer activity.
     * Active streamers (recent donations) get shorter TTL for real-time updates.
     * Inactive streamers get longer TTL to reduce database load.
     *
     * Semua aggregasi dilakukan di SQL — tidak ada get() ke memori.
     *
     * @return array{
     *     total: int,
     *     count: int,
     *     donors: int,
     *     ytCount: int,
     *     biggest: array{name: string, amount: int}|null,
     *     leaderboard: array,
     *     milestone: array{target: int, title: string, current: int, reached: bool},
     *     config: array,
     *     subathon: array
     * }
     *
     * @see \App\Services\StreamerStatsService For the underlying implementation
     */
    public function buildStats(): array
    {
        return app(\App\Services\StreamerStatsService::class)->buildStats($this);
    }

    /**
     * Calculate dynamic cache TTL based on streamer activity.
     *
     * Active streamers (recent donations) get shorter TTL for real-time updates.
     * Inactive streamers get longer TTL to reduce database load.
     *
     * @return int TTL in seconds
     *
     * @deprecated Use StreamerStatsService::calculateDynamicCacheTtl() instead
     */
    private function calculateDynamicCacheTtl(): int
    {
        // Check last donation time (use simple DB query, very fast with index)
        $lastDonationAt = $this->donations()
            ->latest('created_at')
            ->value('created_at');

        if (!$lastDonationAt) {
            // No donations: long cache (5 minutes)
            return config('cache-ttl.streamer_stats_inactive', 300);
        }

        $lastDonation = \Carbon\Carbon::parse($lastDonationAt);
        $minutesSince = now()->diffInMinutes($lastDonation);

        // Dynamic TTL based on recency
        return match(true) {
            $minutesSince < 5   => config('cache-ttl.streamer_stats_ttl', 15),      // Active: 15s
            $minutesSince < 30  => config('cache-ttl.streamer_stats_recent', 60),   // Recent: 1 min
            $minutesSince < 120 => config('cache-ttl.streamer_stats_idle', 180),    // Idle: 3 min
            default             => config('cache-ttl.streamer_stats_inactive', 300), // Inactive: 5 min
        };
    }
}
