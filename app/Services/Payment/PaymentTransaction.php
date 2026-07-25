<?php

namespace App\Services\Payment;

final class PaymentTransaction
{
    public function __construct(
        public readonly string $token,
        public readonly string $orderId,
    ) {}
}
