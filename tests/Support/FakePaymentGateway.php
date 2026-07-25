<?php

namespace Tests\Support;

use App\Models\Donation;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\PaymentNotification;
use App\Services\Payment\PaymentTransaction;
use RuntimeException;

class FakePaymentGateway implements PaymentGatewayInterface
{
    public bool $shouldThrowOnCreate = false;

    public function createTransaction(Donation $donation): PaymentTransaction
    {
        if ($this->shouldThrowOnCreate) {
            throw new RuntimeException('Fake gateway: simulated createTransaction failure.');
        }

        return new PaymentTransaction(
            token: "fake-snap-token-{$donation->id}",
            orderId: "TRX-{$donation->id}",
        );
    }

    public function verifyNotification(array $payload): PaymentNotification
    {
        return new PaymentNotification(
            orderId: (string) $payload['order_id'],
            status: (string) $payload['status'],
            paymentType: $payload['payment_type'] ?? null,
        );
    }
}
