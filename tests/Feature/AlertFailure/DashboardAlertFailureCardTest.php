<?php

namespace Tests\Feature\AlertFailure;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAlertFailureCardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_card_renders_when_there_are_unresolved_failures(): void
    {
        $admin = $this->admin();
        ActivityLog::log(action: 'donation.alert_failed', payload: ['donation_id' => 1, 'error' => 'x']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        // "Alert Gagal" alone also matches the nav link (present on every admin
        // page regardless of this card) — the em dash is unique to the card copy.
        $response->assertSee('1 Alert Gagal —', false);
    }

    public function test_card_is_absent_when_there_are_no_unresolved_failures(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertDontSee('Alert Gagal —', false);
    }
}
