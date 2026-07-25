<?php

namespace Tests\Feature\Donation;

use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationPaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_donation_defaults_to_pending_status(): void
    {
        $donation = Donation::factory()->create();

        $this->assertSame('pending', $donation->status);
    }

    public function test_paid_scope_only_returns_paid_donations(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending']);
        $paid = Donation::factory()->for($streamer)->create(['status' => 'paid']);

        $result = Donation::paid()->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($paid));
    }

    public function test_streamer_paid_donations_relation_filters_by_status(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'failed']);
        $paid = Donation::factory()->for($streamer)->create(['status' => 'paid']);

        $result = $streamer->paidDonations()->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($paid));
    }
}
