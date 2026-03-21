<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SourceSeeder::class,
            UserSeeder::class,
            UserPreferredSourceSeeder::class,
        ]);

        Artisan::call('app:get-article-command');

        $this->call([
            UserPreferredCategorySeeder::class,
            UserPreferredAuthorSeeder::class,
        ]);
    }
}
