<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Staff;

class StudentManagementAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admission_services_officer_can_access_student_management()
    {
        // Create user and staff
        $user = User::factory()->create([
            'email' => 'kristinelabang@ustp.edu.ph',
            'role' => 2,
            'designation' => 'Admission Services Officer',
        ]);
        Staff::factory()->create([
            'email' => 'kristinelabang@ustp.edu.ph',
            'designation' => 'Admission Services Officer',
            'employment_status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/admin/staff/dashboard/Admission%20Services%20Officer/student-management');
        $response->assertStatus(200);
        $response->assertSee('Student Management');
    }
}
