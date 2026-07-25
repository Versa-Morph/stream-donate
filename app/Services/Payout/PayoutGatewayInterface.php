<?php

namespace App\Services\Payout;

use App\Models\Payout;

interface PayoutGatewayInterface
{
    public function validateBankAccount(Payout $payout): bool;

    public function disburse(Payout $payout): PayoutDisbursementResult;

    public function checkStatus(Payout $payout): PayoutStatusResult;
}
