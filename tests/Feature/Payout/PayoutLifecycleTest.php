<?php

namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_mark_paid_sets_status_and_reference(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/mark-paid", [
            'reference' => 'TRF-20260725-001',
        ]);

        $response->assertSessionHasNoErrors();
        $payout->refresh();
        $this->assertSame('paid', $payout->status);
        $this->assertSame('TRF-20260725-001', $payout->reference);
        $this->assertNotNull($payout->paid_at);
    }

    public function test_mark_paid_on_already_paid_payout_is_rejected(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'paid', 'paid_at' => now(), 'reference' => 'OLD-REF']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/mark-paid", [
            'reference' => 'NEW-REF',
        ]);

        $response->assertSessionHasErrors();
        $payout->refresh();
        $this->assertSame('OLD-REF', $payout->reference);
    }

    public function test_void_releases_donations_back_to_unpaid_out(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        $payout = Payout::factory()->for($streamer)->create(['status' => 'pending']);
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 50000, 'payout_id' => $payout->id]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/void");

        $response->assertSessionHasNoErrors();
        $payout->refresh();
        $this->assertSame('voided', $payout->status);

        $donation->refresh();
        $this->assertNull($donation->payout_id);
        $this->assertSame(50000, $streamer->unpaidOutDonations()->sum('amount'));
    }

    public function test_void_on_paid_payout_is_rejected(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'paid', 'paid_at' => now(), 'reference' => 'REF']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/void");

        $response->assertSessionHasErrors();
        $payout->refresh();
        $this->assertSame('paid', $payout->status);
    }
}
