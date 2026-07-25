<?php

namespace Tests\Feature\PayoutDisbursement;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankCodeSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_a_valid_bank_code_succeeds(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => $streamer->display_name,
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
            'bank_name' => 'bca',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);

        $response->assertSessionHasNoErrors();
        $streamer->refresh();
        $this->assertSame('bca', $streamer->bank_name);
    }

    public function test_saving_an_unknown_bank_code_is_rejected(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => $streamer->display_name,
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
            'bank_name' => 'not-a-real-bank',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);

        $response->assertSessionHasErrors(['bank_name']);
    }

    public function test_bank_display_name_looks_up_the_friendly_name(): void
    {
        $streamer = Streamer::factory()->create(['bank_name' => 'bca']);

        $this->assertSame(config('banks')['bca'], $streamer->bankDisplayName());
    }

    public function test_bank_display_name_falls_back_gracefully_when_unset(): void
    {
        $streamer = Streamer::factory()->create(['bank_name' => null]);

        $this->assertSame('-', $streamer->bankDisplayName());
    }
}
