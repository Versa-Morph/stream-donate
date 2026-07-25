<?php

namespace Tests\Feature\AlertFailure;

use App\Models\ActivityLog;
use App\Models\AlertQueue;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAlertFailureControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_index_shows_unresolved_failure_with_retry_form(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'name' => 'Andi']);
        ActivityLog::log(
            action: 'donation.alert_failed',
            payload: ['donation_id' => $donation->id, 'error' => 'Queue timeout'],
        );

        $response = $this->actingAs($admin)->get('/admin/alert-failures');

        $response->assertOk();
        $response->assertSee('Andi');
        $response->assertSee('Queue timeout');
        $response->assertSee(route('admin.alert-failures.retry', $donation));
    }

    public function test_index_handles_deleted_donation_gracefully(): void
    {
        $admin = $this->admin();
        ActivityLog::log(
            action: 'donation.alert_failed',
            payload: ['donation_id' => 999999, 'error' => 'Queue timeout'],
        );

        $response = $this->actingAs($admin)->get('/admin/alert-failures');

        $response->assertOk();
        $response->assertSee('Donasi telah dihapus');
    }

    public function test_retry_creates_alert_queue_and_logs_success(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid']);
        ActivityLog::log(
            action: 'donation.alert_failed',
            payload: ['donation_id' => $donation->id, 'error' => 'Queue timeout'],
        );

        $response = $this->actingAs($admin)->post("/admin/alert-failures/{$donation->id}/retry");

        $response->assertSessionHasNoErrors();
        $this->assertSame(1, AlertQueue::where('donation_id', $donation->id)->count());
        $this->assertDatabaseHas('activity_logs', ['action' => 'donation.alert_retried']);

        $unresolved = app(\App\Services\AlertFailureService::class)->unresolved();
        $this->assertCount(0, $unresolved);
    }
}
