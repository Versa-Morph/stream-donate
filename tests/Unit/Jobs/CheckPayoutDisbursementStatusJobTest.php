<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CheckPayoutDisbursementStatusJob;
use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Services\Payout\PayoutGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakePayoutGateway;
use Tests\TestCase;

class CheckPayoutDisbursementStatusJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Binds FakePayoutGateway for just this test — the job resolves
     * PayoutGatewayInterface itself (not via constructor injection like the
     * controller), so this needs to be bound before the job runs regardless
     * of the automated_disbursement_enabled flag (this job only exists to
     * poll payouts that are already `processing`, which only happens when
     * automation was on at creation time — but the job itself doesn't
     * re-check the flag, it just polls whatever gateway is bound).
     */
    private function fakeGateway(): FakePayoutGateway
    {
        $fake = new FakePayoutGateway();
        $this->app->singleton(PayoutGatewayInterface::class, fn () => $fake);
        return $fake;
    }

    public function test_processing_payout_resolves_to_paid(): void
    {
        $gateway = $this->fakeGateway();
        $gateway->checkStatusResult = 'paid';

        $payout = Payout::factory()->create(['status' => 'processing']);

        (new CheckPayoutDisbursementStatusJob())->handle();

        $payout->refresh();
        $this->assertSame('paid', $payout->status);
        $this->assertNotNull($payout->paid_at);
    }

    public function test_processing_payout_resolves_to_failed_and_releases_donations(): void
    {
        $gateway = $this->fakeGateway();
        $gateway->checkStatusResult = 'failed';

        $streamer = Streamer::factory()->create();
        $payout = Payout::factory()->for($streamer)->create(['status' => 'processing']);
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'payout_id' => $payout->id]);

        (new CheckPayoutDisbursementStatusJob())->handle();

        $payout->refresh();
        $this->assertSame('failed', $payout->status);
        $donation->refresh();
        $this->assertNull($donation->payout_id);
    }

    public function test_non_processing_payouts_are_untouched(): void
    {
        $gateway = $this->fakeGateway();
        $gateway->checkStatusResult = 'paid'; // would resolve to paid if (wrongly) picked up

        $pending = Payout::factory()->create(['status' => 'pending']);
        $originalPaidAt = now()->subDay();
        $alreadyPaid = Payout::factory()->create(['status' => 'paid', 'paid_at' => $originalPaidAt]);

        (new CheckPayoutDisbursementStatusJob())->handle();

        $this->assertSame('pending', $pending->fresh()->status);
        $this->assertSame('paid', $alreadyPaid->fresh()->status);
        $this->assertSame($originalPaidAt->toDateTimeString(), $alreadyPaid->fresh()->paid_at->toDateTimeString());
    }
}
