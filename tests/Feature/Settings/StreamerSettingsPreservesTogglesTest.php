<?php

namespace Tests\Feature\Settings;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerSettingsPreservesTogglesTest extends TestCase
{
    use RefreshDatabase;

    private function streamerUser(): array
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create([
            'user_id' => $user->id,
            'is_accepting_donation' => true,
            'media_upload_enabled' => true,
        ]);

        return [$user, $streamer];
    }

    public function test_saving_settings_does_not_disable_accepting_donations(): void
    {
        [$user, $streamer] = $this->streamerUser();

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => 'Updated Name',
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue($streamer->fresh()->is_accepting_donation);
    }

    public function test_saving_settings_does_not_disable_media_upload(): void
    {
        [$user, $streamer] = $this->streamerUser();

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => 'Updated Name',
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue($streamer->fresh()->media_upload_enabled);
    }
}
