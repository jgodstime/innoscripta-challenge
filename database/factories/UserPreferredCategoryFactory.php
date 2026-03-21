<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Models\UserPreferredCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPreferredCategory>
 */
class UserPreferredCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryId = Category::query()->inRandomOrder()->value('id');
        if ($categoryId === null) {
            $categoryId = Category::factory()->create()->id;
        }

        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'category_id' => $categoryId,
        ];
    }
}
