<?php
namespace Tests\Feature\Donation;

use App\Models\Streamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonateShowRendersTest extends TestCase
{
    use RefreshDatabase;

    public function test_donate_show_renders_with_snap_script(): void
    {
        $streamer = Streamer::factory()->create();

        $response = $this->get("/{$streamer->slug}");

        $response->assertOk();
        $response->assertSee('snap.js', false);
    }
}
