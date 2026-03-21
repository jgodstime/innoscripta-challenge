<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = array_keys(config('article.providers', []));

        foreach ($providers as $providerKey) {
           
            Source::firstOrCreate(
                [
                    'class_key' => $providerKey,
                ],
                [
                    'name' => str_replace('_', ' ', ucfirst($providerKey)),
                    'slug' => Str::slug($providerKey),
                ]);
        }
    }
}
