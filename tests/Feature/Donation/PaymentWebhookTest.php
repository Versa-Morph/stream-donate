<?php

namespace Tests\Feature\Donation;

use App\Models\AlertQueue;
use App\Models\Donation;
use App\Models\Streamer;
use App\Services\Payment\MidtransSnapGateway;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_notification_marks_donation_paid_and_queues_alert(): void
    {
        $streamer = Streamer::factory()->create();
        $donation = Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'payment_reference' => 'TRX-1',
        ]);

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'TRX-1',
            'status' => 'paid',
            'payment_type' => 'qris',
        ]);

        $response->assertOk();
        $donation->refresh();
        $this->assertSame('paid', $donation->status);
        $this->assertSame('qris', $donation->payment_type);
        $this->assertNotNull($donation->paid_at);
        $this->assertSame(1, AlertQueue::where('donation_id', $donation->id)->count());
    }

    public function test_duplicate_paid_notification_does_not_double_credit(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'payment_reference' => 'TRX-2',
        ]);

        $payload = ['order_id' => 'TRX-2', 'status' => 'paid', 'payment_type' => 'qris'];

        $this->postJson('/webhooks/midtrans', $payload)->assertOk();
        $this->postJson('/webhooks/midtrans', $payload)->assertOk();

        $this->assertSame(1, AlertQueue::count());
    }

    public function test_failed_notification_marks_failed_without_alert(): void
    {
        $streamer = Streamer::factory()->create();
        $donation = Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'payment_reference' => 'TRX-3',
        ]);

        $this->postJson('/webhooks/midtrans', [
            'order_id' => 'TRX-3',
            'status' => 'failed',
        ])->assertOk();

        $donation->refresh();
        $this->assertSame('failed', $donation->status);
        $this->assertSame(0, AlertQueue::count());
    }

    public function test_unknown_order_id_returns_404(): void
    {
        $this->postJson('/webhooks/midtrans', [
            'order_id' => 'TRX-does-not-exist',
            'status' => 'paid',
        ])->assertStatus(404);
    }

    public function test_invalid_signature_is_rejected_with_403(): void
    {
        $this->app->singleton(PaymentGatewayInterface::class, MidtransSnapGateway::class);
        config(['midtrans.server_key' => 'test-server-key']);

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'TRX-999',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'signature_key' => 'not-a-real-signature',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
    }
}
