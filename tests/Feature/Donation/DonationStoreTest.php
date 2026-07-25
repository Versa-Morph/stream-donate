<?php

namespace Tests\Feature\Donation;

use App\Models\AlertQueue;
use App\Models\Donation;
use App\Models\Streamer;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_submission_creates_pending_donation_and_returns_snap_token(): void
    {
        $streamer = Streamer::factory()->create();

        $response = $this->postJson("/{$streamer->slug}/donate", [
            'name' => 'Budi',
            'amount' => 20000,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['data' => ['donation_id', 'snap_token']]);

        $donation = Donation::firstOrFail();
        $this->assertSame('pending', $donation->status);
        $this->assertSame("TRX-{$donation->id}", $donation->payment_reference);
        $this->assertSame(0, AlertQueue::count());
    }

    public function test_gateway_failure_returns_error_and_leaves_donation_pending(): void
    {
        $streamer = Streamer::factory()->create();

        $fake = $this->app->make(PaymentGatewayInterface::class);
        $fake->shouldThrowOnCreate = true;

        $response = $this->postJson("/{$streamer->slug}/donate", [
            'name' => 'Budi',
            'amount' => 20000,
        ]);

        $response->assertStatus(502);
        $response->assertJson(['success' => false]);

        $donation = Donation::firstOrFail();
        $this->assertSame('pending', $donation->status);
    }
}
