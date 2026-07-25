<?php

namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnpaidOutDonationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpaid_out_donations_only_counts_paid_and_unassigned(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'failed', 'amount' => 999999]);

        $payout = Payout::factory()->for($streamer)->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 999999, 'payout_id' => $payout->id]);

        $unassignedPaid = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 15000]);

        $result = $streamer->unpaidOutDonations()->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($unassignedPaid));
        $this->assertSame(15000, $streamer->unpaidOutDonations()->sum('amount'));
    }
}
