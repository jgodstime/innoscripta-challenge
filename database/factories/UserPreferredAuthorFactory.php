<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\User;
use App\Models\UserPreferredAuthor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPreferredAuthor>
 */
class UserPreferredAuthorFactory extends Factory
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
            'author_id' => Author::query()->inRandomOrder()->value('id'),
        ];
    }
}
