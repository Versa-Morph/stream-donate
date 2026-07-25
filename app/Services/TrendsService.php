<?php

namespace App\Services;

use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class TrendsService
{
    public function donationTrend(int $days): array
    {
        // App timezone is UTC, but donations are grouped by WIB (Asia/Jakarta)
        // calendar day — reasoning in WIB here too keeps the PHP-side lookup
        // keys in agreement with the SQL-side day buckets below. Using plain
        // now() (UTC) here would mismatch the SQL bucketing for roughly 7 of
        // every 24 hours (whenever UTC time is 17:00-23:59 and WIB has
        // already rolled to the next calendar day).
        $nowWib = now('Asia/Jakarta');
        $start = $nowWib->copy()->subDays($days - 1)->startOfDay()->setTimezone('UTC');

        // Same WIB-aware date grouping as StreamerDashboardController::buildMonthHeatmap
        $driver = DB::getDriverName();
        $dateExpr = $driver === 'sqlite'
            ? "DATE(datetime(created_at, '+7 hours'))"
            : "DATE(CONVERT_TZ(created_at, 'UTC', 'Asia/Jakarta'))";

        $rows = Donation::paid()
            ->where('created_at', '>=', $start)
            ->selectRaw("{$dateExpr} as day, SUM(amount) as total, COUNT(*) as cnt")
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $labels = $amounts = $counts = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $nowWib->copy()->subDays($days - 1 - $i);
            $row = $rows->get($date->format('Y-m-d'));
            $labels[] = $date->format('d/m');
            $amounts[] = $row ? (int) $row->total : 0;
            $counts[] = $row ? (int) $row->cnt : 0;
        }

        return compact('labels', 'amounts', 'counts');
    }
}
