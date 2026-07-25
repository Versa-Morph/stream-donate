<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'streamer_id' => Streamer::factory(),
            'name' => fake()->firstName(),
            'amount' => fake()->randomElement([5000, 10000, 25000, 50000]),
            'emoji' => '💝',
            'status' => 'pending',
            'ip_address' => '127.0.0.1',
        ];
    }
}
