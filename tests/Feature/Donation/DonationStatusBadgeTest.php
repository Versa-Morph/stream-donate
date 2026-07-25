<?php

namespace Tests\Feature\Donation;

use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationStatusBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_donations_list_shows_status_badge(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'name' => 'Andi']);
        Donation::factory()->for($streamer)->create(['status' => 'failed', 'name' => 'Sari']);

        $response = $this->actingAs($admin)->get('/admin/donations');

        $response->assertOk();
        $response->assertSee('Menunggu Pembayaran');
        $response->assertSee('Gagal');
    }

    public function test_streamer_dashboard_history_shows_status_badge(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);
        Donation::factory()->for($streamer)->create(['status' => 'expired', 'name' => 'Rudi']);

        $response = $this->actingAs($user)->get('/streamer/dashboard');

        $response->assertOk();
        $response->assertSee('Kedaluwarsa');
    }
}
