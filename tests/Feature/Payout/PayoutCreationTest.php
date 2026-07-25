<?php

namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutCreationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    private function streamerWithBankInfo(): Streamer
    {
        return Streamer::factory()->create([
            'bank_name' => 'Bank Central Asia',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);
    }

    public function test_creates_payout_with_correct_amounts_and_assigns_donations(): void
    {
        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        $d1 = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 60000]);
        $d2 = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 40000]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $response->assertSessionHasNoErrors();
        $payout = Payout::firstOrFail();
        $this->assertSame(100000, $payout->gross_amount);
        $this->assertSame(10000, $payout->platform_fee_amount);
        $this->assertSame(90000, $payout->net_amount);
        $this->assertSame('Bank Central Asia', $payout->bank_name);

        $d1->refresh();
        $d2->refresh();
        $this->assertSame($payout->id, $d1->payout_id);
        $this->assertSame($payout->id, $d2->payout_id);
    }

    public function test_below_minimum_amount_is_rejected(): void
    {
        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 10000]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_missing_bank_info_is_rejected(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create(); // no bank info
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_already_assigned_donation_is_excluded_from_a_new_payout(): void
    {
        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");
        $firstPayout = Payout::firstOrFail();

        $newDonation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 60000]);
        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $this->assertSame(2, Payout::count());
        $secondPayout = Payout::where('id', '!=', $firstPayout->id)->firstOrFail();
        $this->assertSame(60000, $secondPayout->gross_amount);
        $newDonation->refresh();
        $this->assertSame($secondPayout->id, $newDonation->payout_id);
    }
}
