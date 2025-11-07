<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            'Guidance Counsellor',
            'Prefect of Discipline',
            'Librarian',
            'Nurse',
            'OSA Staff',
            'Student Org. Moderator',
            'IMT Coordinator',
            'Admission Services Officer',
            'Carriers Management Officer',
        ];
        foreach ($designations as $name) {
            Designation::firstOrCreate(['name' => $name]);
        }
    }
}
