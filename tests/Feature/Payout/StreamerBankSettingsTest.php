<?php

namespace Tests\Feature\Payout;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerBankSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function streamerUser(): array
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);

        return [$user, $streamer];
    }

    public function test_saving_all_three_bank_fields_succeeds(): void
    {
        [$user, $streamer] = $this->streamerUser();

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => $streamer->display_name,
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
            'bank_name' => 'Bank Central Asia',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);

        $response->assertSessionHasNoErrors();
        $streamer->refresh();
        $this->assertSame('Bank Central Asia', $streamer->bank_name);
        $this->assertSame('1234567890', $streamer->bank_account_number);
        $this->assertSame('Budi Santoso', $streamer->bank_account_holder);
    }

    public function test_saving_only_one_bank_field_is_rejected(): void
    {
        [$user, $streamer] = $this->streamerUser();

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => $streamer->display_name,
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
            'bank_account_number' => '1234567890',
        ]);

        $response->assertSessionHasErrors(['bank_name', 'bank_account_holder']);
        $streamer->refresh();
        $this->assertNull($streamer->bank_account_number);
    }
}
