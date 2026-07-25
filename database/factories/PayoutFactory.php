<?php

namespace Database\Factories;

use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payout>
 */
class PayoutFactory extends Factory
{
    protected $model = Payout::class;

    public function definition(): array
    {
        return [
            'streamer_id' => Streamer::factory(),
            'gross_amount' => 100000,
            'platform_fee_amount' => 10000,
            'net_amount' => 90000,
            'status' => 'pending',
            'bank_name' => 'Bank Central Asia',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => fake()->name(),
            'created_by' => User::factory(),
        ];
    }
}
