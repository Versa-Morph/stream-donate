<?php

namespace Tests\Feature\PayoutDisbursement;

use App\Models\Payout;
use App\Services\Payout\ManualPayoutGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualPayoutGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_gateway_validates_any_account_and_never_disburses(): void
    {
        $payout = Payout::factory()->create();
        $gateway = new ManualPayoutGateway();

        $this->assertTrue($gateway->validateBankAccount($payout));

        $result = $gateway->disburse($payout);
        $this->assertSame('pending', $result->status);
        $this->assertNull($result->reference);
    }
}
