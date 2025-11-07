<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\User;

class StudentRegisterController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'gender' => 'required',
            'birth_date' => 'required|date',
            'course_id' => 'required|exists:courses,id',
            'organization_id' => 'required|exists:organizations,id',
            'year_level' => 'required|integer|min:1|max:5',
            'student_type1' => 'required',
            'student_type2' => 'required',
            'scholarship_id' => 'nullable|exists:scholarships,id',
            'contact_number' => 'required',
            'emergency_contact_name' => 'required',
            'emergency_contact_number' => 'required',
            'emergency_relation' => 'required',
        ]);

        // Create organization registration request (replace with your model logic)
        // OrganizationRegistrationRequest::create([...]);

        // Redirect to dashboard for the selected organization
        $orgId = $request->input('organization_id');
        return redirect()->route('student.dashboard', ['organization' => $orgId])
            ->with('success', 'Organization registration request sent successfully!');
    }
}
