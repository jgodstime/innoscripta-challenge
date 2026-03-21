<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            [
                'email' => 'jgodstime10@gmail.com',
            ],
            [
                'first_name' => 'Godstime',
                'last_name' => 'John',
                'password' => bcrypt('password'),
            ]);
    }
}
