<?php

namespace Tests\Feature\Trends;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrendsEndpointTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_returns_correctly_shaped_json_for_each_valid_range(): void
    {
        $admin = $this->admin();

        foreach ([7, 30, 90] as $days) {
            $response = $this->actingAs($admin)->getJson("/admin/dashboard/trends?days={$days}");

            $response->assertOk();
            $response->assertJsonStructure(['labels', 'amounts', 'counts']);
            $this->assertCount($days, $response->json('labels'));
        }
    }

    public function test_rejects_an_invalid_days_value(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->getJson('/admin/dashboard/trends?days=45');

        $response->assertStatus(422);
    }
}
