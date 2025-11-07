<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'first_name' => 'Admin',
            'middle_name' => null,
            'last_name' => 'User',
            'email' => 'admin@ustp.edu.ph',
            'password' => Hash::make('password'), // Change password after first login
            'role' => 4,
            'contact_number' => '+63 912 345 6789',
            'birth_date' => '1990-01-01', // Always set a default birth_date
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'first_name' => 'Maintenance',
            'middle_name' => null,
            'last_name' => 'User',
            'email' => 'maintenance@ustp.edu.ph',
            'password' => Hash::make('password'), // Change password after first login
            'role' => 4,
            'contact_number' => '+63 912 345 6789',
            'birth_date' => '1990-01-01', // Always set a default birth_date
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        
    }
}
