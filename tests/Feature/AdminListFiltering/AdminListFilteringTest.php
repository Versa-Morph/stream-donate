<?php

namespace Tests\Feature\AdminListFiltering;

use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminListFilteringTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_donations_date_range_narrows_results(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['name' => 'Dalam Rentang', 'created_at' => '2026-07-15']);
        Donation::factory()->for($streamer)->create(['name' => 'Di Luar Rentang', 'created_at' => '2026-06-01']);

        $response = $this->actingAs($admin)->get('/admin/donations?from=2026-07-01&to=2026-07-31');

        $response->assertOk();
        $response->assertSee('Dalam Rentang');
        $response->assertDontSee('Di Luar Rentang');
    }

    public function test_donations_with_no_date_filter_returns_everything(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['name' => 'Donasi Lama', 'created_at' => '2020-01-01']);

        $response = $this->actingAs($admin)->get('/admin/donations');

        $response->assertOk();
        $response->assertSee('Donasi Lama');
    }

    public function test_logs_date_range_and_streamer_filter_combine_with_action_filter(): void
    {
        $admin = $this->admin();
        $streamerA = Streamer::factory()->create();
        $streamerB = Streamer::factory()->create();

        ActivityLog::log(action: 'donation.paid', description: 'Match', streamerId: $streamerA->id);
        ActivityLog::query()->where('description', 'Match')->update(['created_at' => '2026-07-15']);

        ActivityLog::log(action: 'donation.paid', description: 'Streamer Salah', streamerId: $streamerB->id);
        ActivityLog::query()->where('description', 'Streamer Salah')->update(['created_at' => '2026-07-15']);

        ActivityLog::log(action: 'donation.paid', description: 'Tanggal Salah', streamerId: $streamerA->id);
        ActivityLog::query()->where('description', 'Tanggal Salah')->update(['created_at' => '2026-06-01']);

        $response = $this->actingAs($admin)->get(
            "/admin/logs?action=donation&from=2026-07-01&to=2026-07-31&streamer_id={$streamerA->id}"
        );

        $response->assertOk();
        $response->assertSee('Match');
        $response->assertDontSee('Streamer Salah');
        $response->assertDontSee('Tanggal Salah');
    }

    public function test_logs_page_shows_streamer_dropdown(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create(['display_name' => 'Streamer Pilihan']);

        $response = $this->actingAs($admin)->get('/admin/logs');

        $response->assertOk();
        $response->assertSee('Streamer Pilihan');
    }
}
