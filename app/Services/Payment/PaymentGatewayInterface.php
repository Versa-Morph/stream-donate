<?php

namespace App\Services\Payment;

use App\Models\Donation;

interface PaymentGatewayInterface
{
    public function createTransaction(Donation $donation): PaymentTransaction;

    public function verifyNotification(array $payload): PaymentNotification;
}
