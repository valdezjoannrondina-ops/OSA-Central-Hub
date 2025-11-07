<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \DB::table('users')->delete();
        User::create([
            'user_id' => 'test001',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'gender' => 'other', // change as appropriate
            'birth_date' => '1990-01-01', // change as appropriate
            'role' => 1, // change as appropriate
            'designation' => 'Tester', // if required, otherwise remove
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            // Optional: 'remember_token' => Str::random(10),
        ]);
    }
}