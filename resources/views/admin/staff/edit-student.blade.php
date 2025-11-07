@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-12">
      <a href="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student-management') }}" class="btn btn-secondary">&larr; Back to Student Management</a>
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header" style="background-color: midnightblue; color: white;">
          <h5 class="mb-0">Edit Student Details</h5>
        </div>
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          
          <form method="POST" action="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student.update', $student->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Personal Data Sheet Image -->
            <div class="row mb-3">
              <div class="col-12">
                <label for="personal_data_sheet_image" class="form-label">Personal Data Sheet Image</label>
                <input type="file" class="form-control" id="personal_data_sheet_image" name="personal_data_sheet_image" accept="image/*">
                <small class="form-text text-muted">Upload a new image to replace the existing one (optional)</small>
                @if($student->personal_data_sheet_image || ($student->user && $student->user->image))
                  @php
                    $currentImagePath = $student->personal_data_sheet_image ?? ($student->user ? $student->user->image : null);
                  @endphp
                  @if($currentImagePath)
                    <div class="mt-2">
                      <strong>Current Image:</strong><br>
                      <img src="{{ Storage::url($currentImagePath) }}" alt="Current Image" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                  @endif
                @endif
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="user_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="user_id" name="user_id" value="{{ old('user_id', $student->user->user_id ?? $student->user_id ?? '') }}" required>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->email ?? $student->user->email ?? '') }}" required>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-4">
                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" required>
              </div>
              <div class="col-md-4">
                <label for="middle_name" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ old('middle_name', $student->middle_name ?? '') }}">
              </div>
              <div class="col-md-4">
                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" required>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-4">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number', $student->contact_number ?? $student->user->contact_number ?? '') }}">
              </div>
              <div class="col-md-4">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-control" id="gender" name="gender">
                  <option value="">Select</option>
                  <option value="male" {{ old('gender', $student->gender ?? $student->user->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                  <option value="female" {{ old('gender', $student->gender ?? $student->user->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                  <option value="other" {{ old('gender', $student->gender ?? $student->user->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="birth_date" class="form-label">Birth Date</label>
                <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date ?? $student->user->birth_date ?? '') }}">
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-4">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" id="age" name="age" value="{{ old('age', $student->age ?? $student->user->age ?? '') }}" min="1" max="100">
              </div>
              <div class="col-md-4">
                <label for="civil_status" class="form-label">Civil Status</label>
                <select class="form-control" id="civil_status" name="civil_status">
                  <option value="">Select</option>
                  <option value="single" {{ old('civil_status', $student->civil_status ?? $student->user->civil_status ?? '') == 'single' ? 'selected' : '' }}>Single</option>
                  <option value="married" {{ old('civil_status', $student->civil_status ?? $student->user->civil_status ?? '') == 'married' ? 'selected' : '' }}>Married</option>
                  <option value="divorced" {{ old('civil_status', $student->civil_status ?? $student->user->civil_status ?? '') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                  <option value="widowed" {{ old('civil_status', $student->civil_status ?? $student->user->civil_status ?? '') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="place_of_birth" class="form-label">Place of Birth</label>
                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth', $student->place_of_birth ?? $student->user->place_of_birth ?? '') }}" placeholder="City, Province">
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-12">
                <label for="complete_home_address" class="form-label">Complete Home Address</label>
                <textarea class="form-control" id="complete_home_address" name="complete_home_address" rows="2" placeholder="Street, Barangay, City, Province">{{ old('complete_home_address', $student->complete_home_address ?? $student->user->complete_home_address ?? '') }}</textarea>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="parent_spouse_guardian" class="form-label">Parent/Spouse/Guardian</label>
                <input type="text" class="form-control" id="parent_spouse_guardian" name="parent_spouse_guardian" value="{{ old('parent_spouse_guardian', $student->parent_spouse_guardian ?? $student->user->parent_spouse_guardian ?? '') }}" placeholder="Full Name">
              </div>
              <div class="col-md-6">
                <label for="parent_spouse_guardian_address" class="form-label">Parent/Spouse/Guardian Address</label>
                <textarea class="form-control" id="parent_spouse_guardian_address" name="parent_spouse_guardian_address" rows="2" placeholder="Complete Address">{{ old('parent_spouse_guardian_address', $student->parent_spouse_guardian_address ?? $student->user->parent_spouse_guardian_address ?? '') }}</textarea>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-4">
                <label for="student_type1" class="form-label">Student Type 1</label>
                <select class="form-control" id="student_type1" name="student_type1">
                  <option value="">Select Type</option>
                  <option value="regular" {{ old('student_type1', $student->student_type1 ?? $student->user->student_type1 ?? '') == 'regular' ? 'selected' : '' }}>Regular</option>
                  <option value="irregular" {{ old('student_type1', $student->student_type1 ?? $student->user->student_type1 ?? '') == 'irregular' ? 'selected' : '' }}>Irregular</option>
                  <option value="transferee" {{ old('student_type1', $student->student_type1 ?? $student->user->student_type1 ?? '') == 'transferee' ? 'selected' : '' }}>Transferee</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="student_type2" class="form-label">Student Type 2</label>
                <select class="form-control" id="student_type2" name="student_type2">
                  <option value="">Select Type</option>
                  <option value="paying" {{ old('student_type2', $student->student_type2 ?? $student->user->student_type2 ?? '') == 'paying' ? 'selected' : '' }}>Paying</option>
                  <option value="scholar" {{ old('student_type2', $student->student_type2 ?? $student->user->student_type2 ?? '') == 'scholar' ? 'selected' : '' }}>Scholar</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="scholarship_id" class="form-label">Scholarship</label>
                <select class="form-control" id="scholarship_id" name="scholarship_id">
                  <option value="">Select Scholarship</option>
                  @foreach($scholarships as $scholarship)
                    <option value="{{ $scholarship->id }}" {{ old('scholarship_id', $student->scholarship_id ?? $student->user->scholarship_id ?? '') == $scholarship->id ? 'selected' : '' }}>{{ $scholarship->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                <select class="form-control" id="department_id" name="department_id" required>
                  <option value="">Select Department</option>
                  @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id', $student->department_id ?? $student->user->department_id ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                <select class="form-control" id="course_id" name="course_id" required>
                  <option value="">Select Course</option>
                  @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id', $student->course_id ?? $student->user->course_id ?? '') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="organization_id" class="form-label">Organization</label>
                @php
                  $currentOrg = $student->organization ?? $student->user->organization ?? null;
                  $isDepartmentRelated = $currentOrg && $currentOrg->department_id !== null;
                @endphp
                @if($isDepartmentRelated)
                  <select class="form-control" id="organization_id" name="organization_id" disabled>
                    <option value="{{ $currentOrg->id }}" selected>{{ $currentOrg->name }}</option>
                  </select>
                  <input type="hidden" name="organization_id" value="{{ $currentOrg->id }}">
                  <small class="form-text text-muted">
                    <i class="bi bi-info-circle"></i> This organization is automatically assigned based on the student's department. It will be updated automatically if you change the department.
                  </small>
                @else
                  <select class="form-control" id="organization_id" name="organization_id">
                    <option value="">Select Organization</option>
                    @foreach($organizations as $org)
                      <option value="{{ $org->id }}" {{ old('organization_id', $student->organization_id ?? $student->user->organization_id ?? '') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                  </select>
                  <small class="form-text text-muted">
                    Non-academic organization (can be manually selected)
                  </small>
                @endif
              </div>
              <div class="col-md-6">
                <label for="year_level" class="form-label">Year Level</label>
                <input type="number" class="form-control" id="year_level" name="year_level" value="{{ old('year_level', $student->year_level ?? $student->user->year_level ?? '') }}" min="1" max="5">
              </div>
            </div>
            
            <!-- Resend Verification Checkbox -->
            <div class="row mb-4">
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="resend_verification" name="resend_verification" value="1">
                  <label class="form-check-label" for="resend_verification">
                    <strong>Resend verification email</strong> - Send a new temporary password to the student's email address
                  </label>
                </div>
                <small class="form-text text-muted">
                  Check this box to generate a new temporary password and send it to the student's email address. This is useful if the student missed the initial verification email.
                </small>
              </div>
            </div>
            
            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary">Update Student</button>
                <a href="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student-management') }}" class="btn btn-secondary">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
