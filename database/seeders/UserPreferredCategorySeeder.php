<?php

namespace Database\Seeders;

use App\Models\UserPreferredCategory;
use Illuminate\Database\Seeder;

class UserPreferredCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }
        // Category::factory()->count(1)->create();
        UserPreferredCategory::factory()->count(1)->create();
    }
}
