<?php

namespace Tests\Unit\Services\Payment;

use App\Services\Payment\InvalidPaymentSignatureException;
use App\Services\Payment\MidtransSnapGateway;
use Tests\TestCase;

class MidtransSnapGatewayTest extends TestCase
{
    public function test_verify_notification_accepts_valid_signature_and_maps_settlement_to_paid(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);
        $gateway = new MidtransSnapGateway();

        $orderId = 'TRX-1';
        $statusCode = '200';
        $grossAmount = '10000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . 'SB-Mid-server-test123');

        $notification = $gateway->verifyNotification([
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ]);

        $this->assertSame('paid', $notification->status);
        $this->assertSame($orderId, $notification->orderId);
        $this->assertSame('qris', $notification->paymentType);
    }

    public function test_verify_notification_rejects_tampered_signature(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);
        $gateway = new MidtransSnapGateway();

        $this->expectException(InvalidPaymentSignatureException::class);

        $gateway->verifyNotification([
            'order_id' => 'TRX-1',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'signature_key' => 'not-the-real-signature',
            'transaction_status' => 'settlement',
        ]);
    }

    public function test_capture_with_challenge_fraud_status_stays_pending(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);
        $gateway = new MidtransSnapGateway();

        $orderId = 'TRX-2';
        $statusCode = '200';
        $grossAmount = '10000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . 'SB-Mid-server-test123');

        $notification = $gateway->verifyNotification([
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'capture',
            'fraud_status' => 'challenge',
        ]);

        $this->assertSame('pending', $notification->status);
    }

    public function test_deny_maps_to_failed_and_expire_maps_to_expired(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);
        $gateway = new MidtransSnapGateway();

        $sign = fn (string $orderId, string $statusCode, string $grossAmount) =>
            hash('sha512', $orderId . $statusCode . $grossAmount . 'SB-Mid-server-test123');

        $deny = $gateway->verifyNotification([
            'order_id' => 'TRX-3', 'status_code' => '202', 'gross_amount' => '10000.00',
            'signature_key' => $sign('TRX-3', '202', '10000.00'),
            'transaction_status' => 'deny',
        ]);
        $this->assertSame('failed', $deny->status);

        $expire = $gateway->verifyNotification([
            'order_id' => 'TRX-4', 'status_code' => '407', 'gross_amount' => '10000.00',
            'signature_key' => $sign('TRX-4', '407', '10000.00'),
            'transaction_status' => 'expire',
        ]);
        $this->assertSame('expired', $expire->status);
    }
}
