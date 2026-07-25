<?php

namespace Tests\Feature\Donation;

use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaidOnlyReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_obs_running_text_only_shows_paid_donation_messages(): void
    {
        // running_text widget is disabled by default (Streamer::getWidgetSettings()) —
        // must explicitly enable it or the view never reaches the donations loop at all.
        $streamer = Streamer::factory()->create([
            'widget_settings' => ['running_text' => ['enabled' => true]],
        ]);
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'message' => 'Pesan belum bayar']);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'message' => 'Pesan sudah bayar']);

        $response = $this->get("/{$streamer->slug}/obs/running-text");

        $response->assertOk();
        $response->assertSee('Pesan sudah bayar');
        $response->assertDontSee('Pesan belum bayar');
    }

    public function test_admin_dashboard_totals_exclude_non_paid_donations(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 10000]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertViewHas('totalAmount', 10000);
        $response->assertViewHas('totalDonations', 1);
    }

    public function test_heatmap_data_excludes_non_paid_donations(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 5000]);

        $response = $this->actingAs($user)->get('/streamer/heatmap-data?year=' . now()->year . '&month=' . now()->month);

        $response->assertOk();
        // heatmapData() returns {year, month, days: [{iso, total, count}, ...]} — one entry
        // per calendar day, not a flat total. Sum across all days rather than assert a
        // single day's bucket, so this doesn't depend on which WIB day the donation lands in.
        $monthTotal = array_sum(array_column($response->json('days'), 'total'));
        $this->assertSame(5000, $monthTotal);
    }
}
