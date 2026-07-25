<?php

namespace Tests\Support;

use App\Models\Payout;
use App\Services\Payout\PayoutDisbursementResult;
use App\Services\Payout\PayoutGatewayInterface;
use App\Services\Payout\PayoutStatusResult;

class FakePayoutGateway implements PayoutGatewayInterface
{
    public bool $bankAccountValid = true;
    public string $disburseStatus = 'processing'; // 'processing' | 'failed'
    public string $checkStatusResult = 'paid'; // 'paid' | 'failed' | 'processing'

    public function validateBankAccount(Payout $payout): bool
    {
        return $this->bankAccountValid;
    }

    public function disburse(Payout $payout): PayoutDisbursementResult
    {
        return new PayoutDisbursementResult(
            status: $this->disburseStatus,
            reference: $this->disburseStatus === 'processing' ? 'FAKE-IRIS-REF-' . $payout->id : null,
            errorMessage: $this->disburseStatus === 'failed' ? 'Simulated disbursement failure' : null,
        );
    }

    public function checkStatus(Payout $payout): PayoutStatusResult
    {
        return new PayoutStatusResult(status: $this->checkStatusResult);
    }
}
