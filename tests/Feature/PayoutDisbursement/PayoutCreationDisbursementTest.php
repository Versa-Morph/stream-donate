<?php

namespace Tests\Feature\PayoutDisbursement;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use App\Services\Payout\PayoutGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakePayoutGateway;
use Tests\TestCase;

class PayoutCreationDisbursementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    private function streamerWithBankInfo(): Streamer
    {
        return Streamer::factory()->create([
            'bank_name' => 'bca',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);
    }

    public function test_flag_off_creates_pending_payout_unchanged(): void
    {
        config(['payout.automated_disbursement_enabled' => false]);
        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $this->assertSame('pending', Payout::firstOrFail()->status);
    }

    /**
     * Binds FakePayoutGateway for just this test (not a global TestCase
     * override — see Task 1 Step 7's note on why ManualPayoutGateway stays
     * the real implementation by default).
     */
    private function fakeGateway(): FakePayoutGateway
    {
        $fake = new FakePayoutGateway();
        $this->app->singleton(PayoutGatewayInterface::class, fn () => $fake);
        return $fake;
    }

    public function test_flag_on_and_disburse_succeeds_moves_to_processing(): void
    {
        config(['payout.automated_disbursement_enabled' => true]);
        $gateway = $this->fakeGateway();
        $gateway->disburseStatus = 'processing';

        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $payout = Payout::firstOrFail();
        $this->assertSame('processing', $payout->status);
        $this->assertSame("FAKE-IRIS-REF-{$payout->id}", $payout->reference);
    }

    public function test_flag_on_and_disburse_fails_releases_donations(): void
    {
        config(['payout.automated_disbursement_enabled' => true]);
        $gateway = $this->fakeGateway();
        $gateway->disburseStatus = 'failed';

        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $payout = Payout::firstOrFail();
        $this->assertSame('failed', $payout->status);
        $donation->refresh();
        $this->assertNull($donation->payout_id);
    }

    public function test_flag_on_and_bank_account_invalid_rejects_before_creating_payout(): void
    {
        config(['payout.automated_disbursement_enabled' => true]);
        $gateway = $this->fakeGateway();
        $gateway->bankAccountValid = false;

        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_mark_paid_accepts_processing_status(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'processing']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/mark-paid", [
            'reference' => 'MANUAL-OVERRIDE-REF',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('paid', $payout->fresh()->status);
    }

    public function test_void_rejects_processing_status(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'processing']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/void");

        $response->assertSessionHasErrors();
        $this->assertSame('processing', $payout->fresh()->status);
    }
}
