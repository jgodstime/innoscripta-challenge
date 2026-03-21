<?php

namespace Database\Factories;

use App\Models\Source;
use App\Models\User;
use App\Models\UserPreferredSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPreferredSource>
 */
class UserPreferredSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'source_id' => Source::query()->inRandomOrder()->value('id'),
        ];
    }
}
