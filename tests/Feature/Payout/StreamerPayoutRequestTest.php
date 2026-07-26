<?php

namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerPayoutRequestTest extends TestCase
{
    use RefreshDatabase;

    private function streamerUser(array $streamerAttrs = []): array
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(array_merge([
            'user_id' => $user->id,
            'bank_name' => 'bca',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ], $streamerAttrs));

        return [$user, $streamer];
    }

    public function test_eligible_streamer_can_request_own_payout(): void
    {
        [$user, $streamer] = $this->streamerUser();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $response = $this->actingAs($user)->post('/streamer/payouts/request');

        $response->assertSessionHasNoErrors();
        $payout = Payout::firstOrFail();
        $this->assertSame($streamer->id, $payout->streamer_id);
        $this->assertSame($user->id, $payout->created_by);
    }

    public function test_below_minimum_is_rejected(): void
    {
        [$user, $streamer] = $this->streamerUser();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 1000]);

        $response = $this->actingAs($user)->post('/streamer/payouts/request');

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_missing_bank_info_is_rejected(): void
    {
        [$user, $streamer] = $this->streamerUser(['bank_name' => null, 'bank_account_number' => null, 'bank_account_holder' => null]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $response = $this->actingAs($user)->post('/streamer/payouts/request');

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_streamer_cannot_affect_another_streamers_payout(): void
    {
        [$requester] = $this->streamerUser();
        [, $otherStreamer] = $this->streamerUser();
        Donation::factory()->for($otherStreamer)->create(['status' => 'paid', 'amount' => 100000]);

        // The route has no streamer-id parameter to tamper with — the request
        // always resolves auth()->user()->streamer. Since $requester's own
        // streamer has no paid donations, their request must fail, proving it
        // was evaluated against their own streamer, not $otherStreamer's.
        $response = $this->actingAs($requester)->post('/streamer/payouts/request');

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }
}
