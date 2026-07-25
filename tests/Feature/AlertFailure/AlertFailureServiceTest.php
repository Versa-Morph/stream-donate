<?php

namespace Tests\Feature\AlertFailure;

use App\Models\ActivityLog;
use App\Services\AlertFailureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertFailureServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_failure_with_no_retry_is_unresolved(): void
    {
        ActivityLog::log(
            action: 'donation.alert_failed',
            description: 'Alert donasi #1 gagal',
            payload: ['donation_id' => 1, 'error' => 'Queue timeout'],
        );

        $unresolved = app(AlertFailureService::class)->unresolved();

        $this->assertCount(1, $unresolved);
        $this->assertSame(1, app(AlertFailureService::class)->unresolvedCount());
    }

    public function test_failure_with_matching_retry_is_excluded(): void
    {
        ActivityLog::log(
            action: 'donation.alert_failed',
            description: 'Alert donasi #2 gagal',
            payload: ['donation_id' => 2, 'error' => 'DB error'],
        );
        ActivityLog::log(
            action: 'donation.alert_retried',
            description: 'Alert donasi #2 berhasil di-retry',
            payload: ['donation_id' => 2],
        );

        $unresolved = app(AlertFailureService::class)->unresolved();

        $this->assertCount(0, $unresolved);
        $this->assertSame(0, app(AlertFailureService::class)->unresolvedCount());
    }

    public function test_retry_for_a_different_donation_does_not_resolve_this_one(): void
    {
        ActivityLog::log(
            action: 'donation.alert_failed',
            description: 'Alert donasi #3 gagal',
            payload: ['donation_id' => 3, 'error' => 'Timeout'],
        );
        ActivityLog::log(
            action: 'donation.alert_retried',
            description: 'Alert donasi #4 berhasil di-retry',
            payload: ['donation_id' => 4],
        );

        $this->assertCount(1, app(AlertFailureService::class)->unresolved());
    }
}
