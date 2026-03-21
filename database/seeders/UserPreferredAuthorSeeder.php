<?php

namespace Database\Seeders;

use App\Models\UserPreferredAuthor;
use Illuminate\Database\Seeder;

class UserPreferredAuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }
        // Author::factory()->count(10)->create();
        UserPreferredAuthor::factory()->count(1)->create();
    }
}
