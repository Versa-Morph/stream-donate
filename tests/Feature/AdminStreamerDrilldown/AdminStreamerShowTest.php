<?php

namespace Tests\Feature\AdminStreamerDrilldown;

use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStreamerShowTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_shows_only_the_requested_streamers_data(): void
    {
        $admin = $this->admin();

        $streamerA = Streamer::factory()->create(['display_name' => 'Streamer A']);
        Donation::factory()->for($streamerA)->create(['status' => 'paid', 'amount' => 25000, 'name' => 'Donatur A']);
        ActivityLog::log(action: 'donation.paid', description: 'Aktivitas A', streamerId: $streamerA->id);

        $streamerB = Streamer::factory()->create(['display_name' => 'Streamer B']);
        Donation::factory()->for($streamerB)->create(['status' => 'paid', 'amount' => 999999, 'name' => 'Donatur B']);
        ActivityLog::log(action: 'donation.paid', description: 'Aktivitas B', streamerId: $streamerB->id);

        $response = $this->actingAs($admin)->get("/admin/streamers/{$streamerA->id}");

        $response->assertOk();
        $response->assertSee('Streamer A');
        $response->assertSee('Donatur A');
        $response->assertSee('Aktivitas A');
        $response->assertDontSee('Donatur B');
        $response->assertDontSee('Aktivitas B');
    }

    public function test_owed_balance_reflects_unpaid_out_donations_only(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 40000]);
        $payout = \App\Models\Payout::factory()->for($streamer)->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 999999, 'payout_id' => $payout->id]);

        $response = $this->actingAs($admin)->get("/admin/streamers/{$streamer->id}");

        $response->assertOk();
        $response->assertSee('40.000');
    }

    public function test_empty_state_renders_without_error(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/streamers/{$streamer->id}");

        $response->assertOk();
    }

    public function test_leaderboard_links_to_the_drilldown_page(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 10000]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee(route('admin.streamers.show', $streamer));
    }
}
