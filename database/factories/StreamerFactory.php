<?php

namespace Database\Factories;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Streamer>
 */
class StreamerFactory extends Factory
{
    protected $model = Streamer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => fake()->unique()->slug(2),
            'display_name' => fake()->name(),
            'api_key' => Streamer::generateApiKey(),
            'min_donation' => 1000,
            'is_accepting_donation' => true,
            'thank_you_message' => 'Terima kasih atas donasi kamu!',
        ];
    }
}
