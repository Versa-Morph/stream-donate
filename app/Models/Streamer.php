<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Streamer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'display_name',
        'api_key',
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
    ];

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
        ];
    }

    public static function generateApiKey(): string
    {
        return Str::random(40) . hash('crc32b', microtime());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function alertQueues(): HasMany
    {
        return $this->hasMany(AlertQueue::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Kembalikan widget_settings dengan nilai default jika null/kosong.
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
                'text'         => '#ffffff',
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
        $defaults = [
            ['from' => 0,       'duration' => 5],
            ['from' => 1000,    'duration' => 8],
            ['from' => 10000,   'duration' => 12],
            ['from' => 100000,  'duration' => 20],
        ];

        return $this->alert_duration_tiers ?: $defaults;
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
     * Kembalikan additional values dengan nilai default jika null/kosong.
     * Default: setiap Rp10.000 = tambah 1 menit
     */
    public function getSubathonAdditionalValuesAttribute($value): array
    {
        $defaults = [
            ['from' => 0, 'minutes' => 1],
            ['from' => 10000, 'minutes' => 2],
            ['from' => 50000, 'minutes' => 5],
            ['from' => 100000, 'minutes' => 10],
            ['from' => 500000, 'minutes' => 30],
        ];

        if (empty($value)) return $defaults;

        $decoded = is_array($value) ? $value : json_decode($value, true);
        if (empty($decoded)) return $defaults;

        return $decoded;
    }

    /**
     * Hitung tambahan menit berdasarkan jumlah donasi.
     * Kembalikan menit tambahan dari tier tertinggi yang `from` <= amount.
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
     * Format timer remaining dalam format HH:MM:SS
     */
    public function getSubathonTimerFormattedAttribute(): string
    {
        $minutes = $this->subathon_current_minutes ?? 0;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d:00', $hours, $mins);
    }

    /**
     * Reset timer ke durasi default
     */
    public function resetSubathonTimer(): void
    {
        $this->subathon_current_minutes = $this->subathon_duration_minutes ?? 60;
        $this->subathon_last_updated = now();
        $this->save();
    }

    /**
     * Tambah waktu ke timer berdasarkan donasi
     */
    public function addSubathonTime(int $donationAmount): array
    {
        if (!$this->subathon_enabled) {
            return ['added' => 0, 'new_total' => $this->subathon_current_minutes];
        }

        $addedMinutes = $this->getSubathonMinutesForAmount($donationAmount);

        if ($addedMinutes > 0) {
            $this->subathon_current_minutes = ($this->subathon_current_minutes ?? 0) + $addedMinutes;
            $this->subathon_last_updated = now();
            $this->save();
        }

        return [
            'added' => $addedMinutes,
            'new_total' => $this->subathon_current_minutes,
        ];
    }

    /**
     * Total donasi semua waktu
     */
    public function getTotalDonationsAttribute(): int
    {
        return $this->donations()->sum('amount');
    }

    /**
     * Total donasi hari ini
     */
    public function getTodayDonationsAttribute(): int
    {
        return $this->donations()->whereDate('created_at', today())->sum('amount');
    }

    /**
     * Statistik lengkap untuk dashboard & SSE
     * Semua aggregasi dilakukan di SQL — tidak ada get() ke memori.
     */
    public function buildStats(): array
    {
        $base = $this->donations();

        // Scalar aggregates — satu query
        $agg = $base->selectRaw(
            'SUM(amount) as total, COUNT(*) as cnt, COUNT(DISTINCT name) as unique_donors, SUM(CASE WHEN yt_url IS NOT NULL THEN 1 ELSE 0 END) as yt_count'
        )->first();

        $total        = (int) ($agg->total         ?? 0);
        $count        = (int) ($agg->cnt            ?? 0);
        $uniqueDonors = (int) ($agg->unique_donors  ?? 0);
        $ytCount      = (int) ($agg->yt_count       ?? 0);

        // Donasi terbesar — satu query ringan
        $biggest = $base->orderByDesc('amount')->first(['name', 'amount']);

        // Leaderboard — aggregasi + limit di SQL
        $leaderboard = $this->donations()
            ->selectRaw('name, MAX(emoji) as emoji, SUM(amount) as total, COUNT(*) as cnt')
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit($this->leaderboard_count)
            ->get()
            ->map(fn ($r) => [
                'name'  => $r->name,
                'emoji' => $r->emoji,
                'total' => (int) $r->total,
                'count' => (int) $r->cnt,
            ])
            ->values()
            ->toArray();

        // Milestone: jika milestone_reset aktif, hanya hitung donasi hari ini
        $milestoneQuery = $this->milestone_reset
            ? $this->donations()->whereDate('created_at', today())
            : $this->donations();
        $milestoneCurrent = (int) $milestoneQuery->sum('amount');

        return [
            'total' => $total,
            'count' => $count,
            'donors' => $uniqueDonors,
            'ytCount' => $ytCount,
            'biggest' => $biggest ? [
                'name' => $biggest->name,
                'amount' => $biggest->amount,
            ] : null,
            'leaderboard' => $leaderboard,
            'milestone' => [
                'target' => $this->milestone_target,
                'title' => $this->milestone_title,
                'current' => $milestoneCurrent,
                'reached' => $milestoneCurrent >= $this->milestone_target,
            ],
            'config' => [
                'milestoneTitle'       => $this->milestone_title,
                'milestoneTarget'      => $this->milestone_target,
                'leaderboardTitle'     => $this->leaderboard_title,
                'leaderboardCount'     => $this->leaderboard_count,
                'alertDuration'        => $this->alert_duration,
                'alertDurationTiers'   => $this->getAlertDurationTiers(),
                'alertMaxDuration'     => min((int) ($this->alert_max_duration ?? 30), 120),
                'ytEnabled'            => $this->yt_enabled,
                'soundEnabled'         => $this->sound_enabled,
                'notificationSound'    => $this->notification_sound,
                'alertTheme'           => $this->alert_theme,
                'alertColors'          => $this->getWidgetSettings()['alert'] ?? [],
            ],
            'subathon' => [
                'enabled'      => $this->subathon_enabled ?? false,
                'currentMinutes' => $this->subathon_current_minutes ?? 0,
                'durationMinutes' => $this->subathon_duration_minutes ?? 60,
                'formatted'    => $this->subathon_timer_formatted,
            ],
        ];
    }
}
