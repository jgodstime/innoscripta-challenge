<?php

namespace Database\Seeders;

use App\Models\UserPreferredSource;
use Illuminate\Database\Seeder;

class UserPreferredSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }
        // Source::factory()->count(10)->create();
        UserPreferredSource::factory()->count(1)->create();
    }
}
