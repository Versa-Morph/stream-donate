<?php

namespace App\Services\Payout;

use App\Models\Payout;

class ManualPayoutGateway implements PayoutGatewayInterface
{
    public function validateBankAccount(Payout $payout): bool
    {
        // Manual mode has no external validation to perform — the existing
        // "has bank_account_number" check in AdminPayoutController::create()
        // is all the validation manual payouts ever had.
        return true;
    }

    public function disburse(Payout $payout): PayoutDisbursementResult
    {
        // No-op: manual mode leaves the payout `pending` for an admin to
        // record the bank transfer and mark it paid by hand.
        return new PayoutDisbursementResult(status: 'pending');
    }

    public function checkStatus(Payout $payout): PayoutStatusResult
    {
        return new PayoutStatusResult(status: $payout->status);
    }
}
