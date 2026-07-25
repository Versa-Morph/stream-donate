<?php

namespace Tests\Feature\Payout;

use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerPayoutHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_streamer_only_sees_their_own_payouts(): void
    {
        $userA = User::factory()->create();
        $userA->forceFill(['role' => 'streamer'])->save();
        $streamerA = Streamer::factory()->create(['user_id' => $userA->id]);
        Payout::factory()->for($streamerA)->create(['net_amount' => 90000]);

        $streamerB = Streamer::factory()->create();
        Payout::factory()->for($streamerB)->create(['net_amount' => 500000]);

        $response = $this->actingAs($userA)->get('/streamer/payouts');

        $response->assertOk();
        $response->assertSee('90.000', false);
        $response->assertDontSee('500.000', false);
    }
}
