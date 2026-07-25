<?php

namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPayoutViewsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_index_shows_streamer_owed_balance_and_existing_payouts(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create(['display_name' => 'Budi Streamer']);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 75000]);
        $payout = Payout::factory()->for($streamer)->create(['net_amount' => 90000]);

        $response = $this->actingAs($admin)->get('/admin/payouts');

        $response->assertOk();
        $response->assertSee('Budi Streamer');
        $response->assertSee('75.000'); // owed balance
        $response->assertSee(route('admin.payouts.show', $payout));
    }

    public function test_show_displays_payout_detail_and_included_donations(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        $payout = Payout::factory()->for($streamer)->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'name' => 'Andi', 'payout_id' => $payout->id]);

        $response = $this->actingAs($admin)->get("/admin/payouts/{$payout->id}");

        $response->assertOk();
        $response->assertSee('Andi');
        $response->assertSee($payout->bank_account_number);
    }
}
