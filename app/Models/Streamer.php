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
            'canvas_config' => 'array',
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
     */
    public function buildStats(): array
    {
        $donations = $this->donations()->orderBy('created_at', 'desc')->get();
        $total = $donations->sum('amount');
        $count = $donations->count();
        $uniqueDonors = $donations->pluck('name')->unique()->count();
        $ytCount = $donations->whereNotNull('yt_url')->count();

        $biggest = $donations->sortByDesc('amount')->first();

        // Leaderboard
        $leaderboard = $donations
            ->groupBy('name')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->name,
                    'emoji' => $group->first()->emoji,
                    'total' => $group->sum('amount'),
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('total')
            ->take($this->leaderboard_count)
            ->values()
            ->toArray();

        // Milestone: jika milestone_reset aktif, hanya hitung donasi hari ini
        $milestoneQuery = $this->milestone_reset
            ? $this->donations()->whereDate('created_at', today())
            : $this->donations();
        $milestoneCurrent = $milestoneQuery->sum('amount');

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
                'milestoneTitle' => $this->milestone_title,
                'milestoneTarget' => $this->milestone_target,
                'leaderboardTitle' => $this->leaderboard_title,
                'leaderboardCount' => $this->leaderboard_count,
                'alertDuration' => $this->alert_duration,
                'ytEnabled' => $this->yt_enabled,
                'soundEnabled' => $this->sound_enabled,
                'notificationSound' => $this->notification_sound,
                'alertTheme' => $this->alert_theme,
            ],
        ];
    }
}
