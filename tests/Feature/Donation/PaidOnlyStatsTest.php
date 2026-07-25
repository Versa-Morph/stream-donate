<?php

namespace Tests\Feature\Donation;

use App\Models\Donation;
use App\Models\Streamer;
use App\Services\StreamerStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaidOnlyStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_stats_ignores_pending_and_failed_donations(): void
    {
        $streamer = Streamer::factory()->create(['leaderboard_count' => 5]);
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'failed', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 15000, 'name' => 'Budi']);

        $stats = app(StreamerStatsService::class)->computeStats($streamer);

        $this->assertSame(15000, $stats['total']);
        $this->assertSame(1, $stats['count']);
        $this->assertCount(1, $stats['leaderboard']);
        $this->assertSame('Budi', $stats['leaderboard'][0]['name']);
    }

    public function test_total_and_today_donation_attributes_are_paid_only(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 10000]);

        $this->assertSame(10000, $streamer->total_donations);
        $this->assertSame(10000, $streamer->today_donations);
    }
}
