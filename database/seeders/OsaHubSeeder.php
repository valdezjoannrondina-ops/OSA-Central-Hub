<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OsaHubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // Disable foreign key checks, truncate tables, then re-enable
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    \DB::table('departments')->truncate();
    \DB::table('courses')->truncate();
    \DB::table('organizations')->truncate();
    \DB::table('scholarships')->truncate();
    \DB::table('users')->truncate();
    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // Departments
    $dept1 = \App\Models\Department::create(['name' => 'Agriculture']);
    $dept2 = \App\Models\Department::create(['name' => 'Information Technology']);
    $dept3 = \App\Models\Department::create(['name' => 'Technology and Livelihood Education']);
    $dept4 = \App\Models\Department::create(['name' => 'General Education']);
    $dept5 = \App\Models\Department::create(['name' => 'Faculty']);
    $dept6 = \App\Models\Department::create(['name' => 'Others']);

    // Courses
    \App\Models\Course::create(['department_id' => $dept1->id, 'name' => 'Bachelor in Agriculture']);
    \App\Models\Course::create(['department_id' => $dept1->id, 'name' => 'Bachelor of Science in Agriculture']);
    \App\Models\Course::create(['department_id' => $dept2->id, 'name' => 'Bachelor of Science in Information Technology']);
    \App\Models\Course::create(['department_id' => $dept3->id, 'name' => 'Bachelor in Technology and Livelihood Education']);
    \App\Models\Course::create(['department_id' => $dept4->id, 'name' => 'GenEd']);
    \App\Models\Course::create(['department_id' => $dept5->id, 'name' => 'Faculty']);
    \App\Models\Course::create(['department_id' => $dept6->id, 'name' => 'Others']);

    // Organizations
    // Academic organizations (department-related organizations - only 3)
    \App\Models\Organization::create([
        'name' => 'Student Council of Agriculture Technology',
        'is_special' => false,
        'department_id' => $dept1->id // Agriculture - Academic Organization
    ]);
    
    \App\Models\Organization::create([
        'name' => 'Student Council of Information Technology',
        'is_special' => false,
        'department_id' => $dept2->id // Information Technology - Academic Organization
    ]);
    
    \App\Models\Organization::create([
        'name' => 'Student Council of Technology and Livelihood Education',
        'is_special' => false,
        'department_id' => $dept3->id // Technology and Livelihood Education - Academic Organization
    ]);
    
    // Non-academic organizations (department_id = null)
    $nonAcademicOrgs = [
        ['name' => 'Supreme Student Council', 'is_special' => true],
        ['name' => 'Federation of Accredited Extra-curicular Student Organizations', 'is_special' => true],
        ['name' => 'Euphonix Band', 'is_special' => true],
        ['name' => 'The Capers', 'is_special' => true],
        ['name' => 'Dumahi-a Dance Troupe', 'is_special' => true],
        ['name' => 'Campus Ministry', 'is_special' => true],
        ['name' => '4H Club', 'is_special' => true],
        ['name' => 'The Masters of Ceremony', 'is_special' => true],
        ['name' => 'Creatives', 'is_special' => true],
        ['name' => 'Technical Organization', 'is_special' => true],
        ['name' => 'CARE Unit (Crisis Aid Rescue & Emergency)', 'is_special' => true],
        ['name' => 'HIMULAK Publication', 'is_special' => true],
    ];
    foreach ($nonAcademicOrgs as $org) {
        \App\Models\Organization::create([
            'name' => $org['name'],
            'is_special' => $org['is_special'],
            'department_id' => null // Non-academic organizations have no department
        ]);
    }

    // Scholarships
    $scholarships = ['CHED-UNIFAST', 'DOST', 'City Scho;ars', 'Others'];
    foreach ($scholarships as $name) {
        \App\Models\Scholarship::create(['name' => $name]);
    }

    // Admin user
    \App\Models\User::create([
        'user_id' => 'admin001',
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@ustp.edu.ph',
        'gender' => 'other',
        'birth_date' => '1990-01-01',
        'role' => 4,
        'designation' => 'Admin',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
}
}
