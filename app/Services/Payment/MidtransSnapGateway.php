<?php

namespace App\Services\Payment;

use App\Models\Donation;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransSnapGateway implements PaymentGatewayInterface
{
    public function createTransaction(Donation $donation): PaymentTransaction
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = "TRX-{$donation->id}";

        $token = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $donation->amount,
            ],
            'customer_details' => [
                'first_name' => $donation->name,
            ],
            'page_expiry' => [
                'duration' => (int) config('midtrans.snap_expiry_minutes', 60),
                'unit' => 'minutes',
            ],
        ]);

        return new PaymentTransaction(token: $token, orderId: $orderId);
    }

    public function verifyNotification(array $payload): PaymentNotification
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $receivedSignature = (string) ($payload['signature_key'] ?? '');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            throw new InvalidPaymentSignatureException('Signature Midtrans tidak valid.');
        }

        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        $status = match (true) {
            $transactionStatus === 'settlement' => 'paid',
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
            $transactionStatus === 'capture' && $fraudStatus === 'challenge' => 'pending',
            in_array($transactionStatus, ['deny', 'cancel'], true) => 'failed',
            $transactionStatus === 'expire' => 'expired',
            default => 'pending',
        };

        return new PaymentNotification(
            orderId: $orderId,
            status: $status,
            paymentType: $payload['payment_type'] ?? null,
        );
    }
}
