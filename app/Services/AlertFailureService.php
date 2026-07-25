<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Collection;

class AlertFailureService
{
    public function unresolved(): Collection
    {
        $retriedDonationIds = ActivityLog::where('action', 'donation.alert_retried')
            ->get()
            ->map(fn ($log) => $log->payload['donation_id'] ?? null)
            ->filter()
            ->all();

        return ActivityLog::where('action', 'donation.alert_failed')
            ->orderByDesc('created_at')
            ->get()
            ->reject(fn ($log) => in_array($log->payload['donation_id'] ?? null, $retriedDonationIds))
            ->values();
    }

    public function unresolvedCount(): int
    {
        return $this->unresolved()->count();
    }
}
