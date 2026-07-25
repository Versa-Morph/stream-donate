<?php

namespace App\Services\Payment;

final class PaymentNotification
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $status,
        public readonly ?string $paymentType,
    ) {}
}
