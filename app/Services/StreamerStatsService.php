<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Streamer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Service for building and caching streamer statistics.
 *
 * This service extracts the stats calculation logic from the Streamer model
 * to follow the Single Responsibility Principle. It provides:
 * - Performance-optimized SQL aggregations (no get() to memory)
 * - Dynamic cache TTL based on streamer activity level
 * - Comprehensive stats for dashboard and SSE endpoints
 */
class StreamerStatsService
{
    /**
     * Build comprehensive statistics for a streamer.
     *
     * PERFORMANCE: Cached with dynamic TTL based on streamer activity.
     * Active streamers (recent donations) get shorter TTL for real-time updates.
     * Inactive streamers get longer TTL to reduce database load.
     *
     * All aggregations performed in SQL - no get() to memory.
     *
     * @param Streamer $streamer The streamer to build stats for
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
     */
    public function buildStats(Streamer $streamer): array
    {
        $ttl = $this->calculateDynamicCacheTtl($streamer);

        return Cache::remember("streamer_stats_{$streamer->id}", $ttl, function () use ($streamer) {
            return $this->computeStats($streamer);
        });
    }

    /**
     * Invalidate the cached stats for a streamer.
     *
     * Call this after donations are received or settings are changed.
     *
     * @param Streamer $streamer The streamer whose cache should be invalidated
     * @return bool True if the cache was successfully forgotten
     */
    public function invalidateCache(Streamer $streamer): bool
    {
        return Cache::forget("streamer_stats_{$streamer->id}");
    }

    /**
     * Compute stats without caching.
     *
     * Used internally by buildStats() and can be called directly
     * when fresh data is absolutely required.
     *
     * @param Streamer $streamer The streamer to compute stats for
     * @return array The computed statistics
     */
    public function computeStats(Streamer $streamer): array
    {
        $base = $streamer->donations();

        // Scalar aggregates — one query
        // SECURITY NOTE: Raw SQL used here is SAFE because:
        // - All aggregation functions (SUM, COUNT) are hardcoded
        // - No user input is interpolated into the SQL string
        // - All WHERE conditions use Eloquent's parameter binding
        $agg = $base->selectRaw(
            'SUM(amount) as total, COUNT(*) as cnt, COUNT(DISTINCT name) as unique_donors, SUM(CASE WHEN yt_url IS NOT NULL THEN 1 ELSE 0 END) as yt_count'
        )->first();

        $total        = (int) ($agg->total ?? 0);
        $count        = (int) ($agg->cnt ?? 0);
        $uniqueDonors = (int) ($agg->unique_donors ?? 0);
        $ytCount      = (int) ($agg->yt_count ?? 0);

        // Biggest donation — one lightweight query
        $biggest = $base->orderByDesc('amount')->first(['name', 'amount']);

        // Leaderboard with most recent emoji
        // BUGFIX: Get most recent emoji instead of MAX() which returns lexicographically highest
        // SECURITY NOTE: Raw SQL used here is SAFE because:
        // - The subquery uses whereColumn() which is parameterized by Eloquent
        // - No user input is interpolated into the SQL string
        // - All column names are hardcoded, not user-controlled
        $leaderboard = $streamer->donations()
            ->selectRaw('name, SUM(amount) as total, COUNT(*) as cnt')
            ->selectSub(
                Donation::selectRaw('emoji')
                    ->whereColumn('name', 'donations.name')
                    ->latest('created_at')
                    ->limit(1),
                'emoji'
            )
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit($streamer->leaderboard_count)
            ->get()
            ->map(fn($r) => [
                'name' => $r->name,
                'emoji' => $r->emoji,
                'total' => (int) $r->total,
                'count' => (int) $r->cnt,
            ])
            ->values()
            ->toArray();

        // Milestone: if milestone_reset is active, only count today's donations
        $milestoneQuery = $streamer->milestone_reset
            ? $streamer->donations()->whereDate('created_at', today())
            : $streamer->donations();
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
                'target' => $streamer->milestone_target,
                'title' => $streamer->milestone_title,
                'current' => $milestoneCurrent,
                'reached' => $milestoneCurrent >= $streamer->milestone_target,
            ],
            'config' => [
                'milestoneTitle' => $streamer->milestone_title,
                'milestoneTarget' => $streamer->milestone_target,
                'leaderboardTitle' => $streamer->leaderboard_title,
                'leaderboardCount' => $streamer->leaderboard_count,
                'alertDuration' => $streamer->alert_duration,
                'alertDurationTiers' => $streamer->getAlertDurationTiers(),
                'alertMaxDuration' => min((int) ($streamer->alert_max_duration ?? 30), 120),
                'ytEnabled' => $streamer->yt_enabled,
                'soundEnabled' => $streamer->sound_enabled,
                'notificationSound' => $streamer->notification_sound,
                'alertTheme' => $streamer->alert_theme,
                'alertColors' => $streamer->getWidgetSettings()['alert'] ?? [],
            ],
            'subathon' => [
                'enabled' => $streamer->subathon_enabled ?? false,
                'currentMinutes' => $streamer->subathon_current_minutes ?? 0,
                'durationMinutes' => $streamer->subathon_duration_minutes ?? 60,
                'formatted' => $streamer->subathon_timer_formatted,
            ],
        ];
    }

    /**
     * Calculate dynamic cache TTL based on streamer activity.
     *
     * Active streamers (recent donations) get shorter TTL for real-time updates.
     * Inactive streamers get longer TTL to reduce database load.
     *
     * @param Streamer $streamer The streamer to calculate TTL for
     * @return int TTL in seconds
     */
    private function calculateDynamicCacheTtl(Streamer $streamer): int
    {
        // Check last donation time (use simple DB query, very fast with index)
        $lastDonationAt = $streamer->donations()
            ->latest('created_at')
            ->value('created_at');

        if (!$lastDonationAt) {
            // No donations: long cache (5 minutes)
            return config('cache-ttl.streamer_stats_inactive', 300);
        }

        $lastDonation = Carbon::parse($lastDonationAt);
        $minutesSince = now()->diffInMinutes($lastDonation);

        // Dynamic TTL based on recency
        return match (true) {
            $minutesSince < 5 => config('cache-ttl.streamer_stats_ttl', 15),       // Active: 15s
            $minutesSince < 30 => config('cache-ttl.streamer_stats_recent', 60),   // Recent: 1 min
            $minutesSince < 120 => config('cache-ttl.streamer_stats_idle', 180),   // Idle: 3 min
            default => config('cache-ttl.streamer_stats_inactive', 300),           // Inactive: 5 min
        };
    }
}
