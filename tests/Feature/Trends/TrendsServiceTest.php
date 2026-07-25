<?php

namespace Tests\Feature\Trends;

use App\Models\Donation;
use App\Models\Streamer;
use App\Services\TrendsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrendsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_donation_trend_zero_fills_days_with_no_donations(): void
    {
        $trend = app(TrendsService::class)->donationTrend(7);

        $this->assertCount(7, $trend['labels']);
        $this->assertCount(7, $trend['amounts']);
        $this->assertCount(7, $trend['counts']);
        $this->assertSame([0, 0, 0, 0, 0, 0, 0], $trend['amounts']);
        $this->assertSame([0, 0, 0, 0, 0, 0, 0], $trend['counts']);
    }

    public function test_donation_trend_sums_paid_donations_on_the_correct_day_and_excludes_pending(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 30000, 'created_at' => now()]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 20000, 'created_at' => now()]);
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999, 'created_at' => now()]);

        $trend = app(TrendsService::class)->donationTrend(7);

        $this->assertSame(50000, end($trend['amounts']));
        $this->assertSame(2, end($trend['counts']));
    }

    public function test_donation_trend_excludes_donations_outside_the_window(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create([
            'status' => 'paid',
            'amount' => 99999,
            'created_at' => now()->subDays(10),
        ]);

        $trend = app(TrendsService::class)->donationTrend(7);

        $this->assertSame(0, array_sum($trend['amounts']));
    }
}
