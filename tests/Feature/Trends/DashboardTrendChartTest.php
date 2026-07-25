<?php

namespace Tests\Feature\Trends;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTrendChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_chart_canvases_and_range_buttons(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('id="trend-amount-chart"', false);
        $response->assertSee('id="trend-count-chart"', false);
        $response->assertSee('cdn.jsdelivr.net/npm/chart.js', false);
        $response->assertSee('30 Hari');
    }
}
