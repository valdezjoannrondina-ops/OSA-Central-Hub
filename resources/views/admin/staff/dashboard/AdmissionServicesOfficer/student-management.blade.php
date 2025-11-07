@extends('layouts.app')

@section('title', 'Student Management - Student Information Sheet')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('duplicate_message'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="bi bi-exclamation-triangle"></i> Duplicate Student Detected!</strong>
        <p class="mb-2">{{ session('duplicate_message') }}</p>
        @if(session('duplicate_student_id'))
            <a href="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student.show', session('duplicate_student_id')) }}" class="btn btn-sm btn-primary mt-2">
                <i class="bi bi-eye"></i> View Student Details
            </a>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> Please fix the following errors:
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-12">
      <a href="{{ route('admin.staff.dashboard.designation', ['designation' => 'Admission Services Officer']) }}" class="btn btn-secondary">&larr; Back</a>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3 col-lg-2">
      <div class="list-group mb-3">
        <div class="list-group-item active" style="background-color: midnightblue; border-color: midnightblue;">Quick Actions</div>
        <a href="{{ route('admin.appointments.index') }}" class="list-group-item list-group-item-action">Assigned Appointments</a>
        @php
          $isStaff = (auth()->user()->role ?? 0) == 2;
          $isAdmin = (auth()->user()->role ?? 0) == 4;
          $designationName = 'Admission Services Officer';
        @endphp
        @if($isStaff)
          <a href="{{ route('staff.organizations.index') }}" class="list-group-item list-group-item-action">My Organization</a>
        @endif
        <a href="{{ route('admin.events.index') }}" class="list-group-item list-group-item-action">All Events</a>
        @if($isAdmin)
          <a href="{{ route('admin.events.index') }}#create" class="list-group-item list-group-item-action">Create Event</a>
        @endif
        <a href="{{ route('admin.participants.export') }}" class="list-group-item list-group-item-action">Participants History</a>
        <a href="{{ route('admin.staff.dashboard.report', ['designation' => $designationName]) }}" class="list-group-item list-group-item-action">Reports</a>
        <a href="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student-management') }}" class="list-group-item list-group-item-action">Student Management</a>
      </div>
    </div>
    <main class="col-md-9 col-lg-10">
      <!-- Department Selection Section -->
      <div class="card mb-3">
        <div class="card-header" style="background-color: midnightblue; color: white;">
          <h5 class="mb-0">Add Student by Department</h5>
        </div>
        <div class="card-body">
          <div class="row align-items-end">
            <div class="col-md-6">
              <label for="quick-department-select">Select Department:</label>
              <select id="quick-department-select" class="form-control">
                <option value="">Choose a department...</option>
                @foreach($departments as $dept)
                  <option value="{{ $dept->id }}" data-dept-name="{{ $dept->name }}">{{ $dept->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <button type="button" id="fill-department-btn" class="btn btn-primary" disabled>Auto-Fill Form</button>
              <button type="button" id="clear-department-btn" class="btn btn-secondary" style="display: none;">Clear Selection</button>
            </div>
          </div>
          <div id="department-info" class="mt-2" style="display: none;">
            <small class="text-muted">
              <strong>Selected:</strong> <span id="selected-dept-name"></span> | 
              <strong>Organization:</strong> <span id="selected-org-name"></span>
            </small>
          </div>
        </div>
      </div>

      <style>
        .personal-data-sheet {
          background: white;
          padding: 20px;
          border: 1px solid #ddd;
        }
        .form-header {
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          margin-bottom: 20px;
          border-bottom: 2px solid #333;
          padding-bottom: 15px;
        }
        .university-info {
          flex: 1;
        }
        .university-name {
          font-weight: bold;
          font-size: 0.9rem;
          margin-top: 5px;
        }
        .campus-locations {
          font-size: 0.75rem;
          color: #666;
          margin-top: 3px;
        }
        .document-info {
          text-align: right;
          font-size: 0.85rem;
        }
        .doc-code-box {
          background: #f0f0f0;
          padding: 8px 12px;
          border: 1px solid #ccc;
          margin-bottom: 8px;
          display: inline-block;
        }
        .doc-meta-box {
          background: #f0f0f0;
          padding: 5px 10px;
          border: 1px solid #ccc;
          font-size: 0.75rem;
        }
        .form-title-section {
          text-align: center;
          margin: 20px 0;
        }
        .form-title {
          font-weight: bold;
          font-size: 1.2rem;
          margin-bottom: 10px;
        }
        .form-section {
          margin-bottom: 20px;
          padding: 10px;
          border-left: 3px solid midnightblue;
          background: #f9f9f9;
        }
        .section-label {
          font-weight: bold;
          font-size: 1rem;
          color: midnightblue;
          margin-bottom: 10px;
        }
        .section-row {
          display: flex;
          gap: 15px;
          margin-bottom: 10px;
          flex-wrap: wrap;
        }
        .form-field {
          display: flex;
          flex-direction: column;
          min-width: 150px;
          flex: 1;
        }
        .form-field label {
          font-size: 0.85rem;
          font-weight: 600;
          margin-bottom: 3px;
        }
        .form-field input,
        .form-field select,
        .form-field textarea {
          border: 1px solid #ccc;
          border-radius: 3px;
          padding: 6px 10px;
        }
        .form-field-full {
          width: 100%;
        }
        .checkbox-group {
          display: flex;
          gap: 20px;
          flex-wrap: wrap;
          margin-top: 10px;
        }
        .checkbox-item {
          display: flex;
          align-items: center;
          gap: 5px;
        }
        .inline-fields {
          display: flex;
          gap: 10px;
          align-items: flex-end;
        }
      </style>
      
      <div class="personal-data-sheet">
        <!-- Form Header -->
        <div class="form-header">
          <div class="university-info">
            <div class="university-name">UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES</div>
            <div class="campus-locations">Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva</div>
          </div>
          <div class="document-info">
            <div class="doc-code-box">Document Code No. FM-USTP-RGTR-03</div>
            <div class="doc-meta-box">
              <div>Rev. No. 00</div>
              <div>Effective Date: 10.01.21</div>
              <div>Page No. 1 of 1</div>
            </div>
          </div>
        </div>

        <!-- Form Title Section -->
        <div class="form-title-section">
          <div class="form-title">Student Information Sheet</div>
          <!-- School Year and Semester/Summer centered below title -->
          <div class="inline-fields" style="justify-content: center; margin-top: 15px;">
            <div class="form-field" style="max-width: 150px;">
              <label>School Year</label>
              <input type="text" name="school_year" class="form-control" placeholder="e.g., 2024-2025">
            </div>
            <div class="form-field" style="max-width: 150px;">
              <label>Semester/Summer</label>
              <select name="semester" class="form-control">
                <option value="">Select</option>
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
                <option value="Summer">Summer</option>
              </select>
            </div>
          </div>
        </div>

        <form action="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student-management.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <!-- Personal Data Sheet Image Upload -->
          <div class="form-section">
            <div class="section-label">Personal Data Sheet Image</div>
            <div class="section-row">
              <div class="form-field form-field-full">
                <label>Upload Photo/Image</label>
                <input type="file" name="personal_data_sheet_image" class="form-control" accept="image/*" id="personal-data-sheet-image">
                <small class="text-muted">Upload student's photo or Personal Data Sheet image (JPG, PNG, etc.)</small>
                <div id="image-preview" class="mt-2" style="display: none;">
                  <img id="preview-img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
              </div>
            </div>
          </div>

          <!-- Student Type and Student ID row - above Section A -->
          <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding: 10px; background: #f9f9f9; border-left: 3px solid midnightblue;">
            <div class="form-field" style="max-width: 200px;">
              <label>Student Type</label>
              <div style="display: flex; gap: 15px; margin-top: 5px;">
                <label style="font-weight: normal;">
                  <input type="radio" name="student_type" value="new" {{ old('student_type') == 'new' ? 'checked' : '' }}> New Student
                </label>
                <label style="font-weight: normal;">
                  <input type="radio" name="student_type" value="old" {{ old('student_type') == 'old' ? 'checked' : '' }}> Old Student
                </label>
              </div>
            </div>
            <div class="form-field" style="max-width: 200px;">
              <label>Student ID No.</label>
              <input type="text" name="user_id" class="form-control" value="{{ old('user_id') }}" placeholder="Student ID" required>
            </div>
          </div>
          
          <!-- Section A: Name -->
          <div class="form-section">
            <div class="section-label">A. NAME</div>
            <div class="section-row">
              <div class="form-field">
                <label>Last Name:</label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
              </div>
              <div class="form-field">
                <label>First Name:</label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
              </div>
              <div class="form-field">
                <label>Middle Name:</label>
                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
              </div>
              <div class="form-field">
                <label>Ext. Name:</label>
                <input type="text" name="ext_name" class="form-control" value="{{ old('ext_name') }}" placeholder="Jr., Sr., III, etc.">
              </div>
            </div>
          </div>

          <!-- Section B: HOME ADDRESS -->
          <div class="form-section">
            <div class="section-label">B. HOME ADDRESS</div>
            <div class="section-row">
              <div class="form-field">
                <label>Street:</label>
                <input type="text" name="street" class="form-control" value="{{ old('street') }}">
              </div>
              <div class="form-field">
                <label>Barangay:</label>
                <input type="text" name="barangay" class="form-control" value="{{ old('barangay') }}">
              </div>
              <div class="form-field">
                <label>City/Municipality:</label>
                <input type="text" name="city_municipality" class="form-control" value="{{ old('city_municipality') }}">
              </div>
              <div class="form-field">
                <label>Province:</label>
                <input type="text" name="province" class="form-control" value="{{ old('province') }}">
              </div>
              <div class="form-field">
                <label>Zip Code:</label>
                <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}">
              </div>
            </div>
          </div>

          <!-- Section C: PERSONAL DETAILS -->
          <div class="form-section">
            <div class="section-label">C. PERSONAL DETAILS</div>
            <div class="section-row">
              <div class="form-field">
                <label>Age:</label>
                <input type="number" name="age" class="form-control" value="{{ old('age') }}" min="1" max="100" required>
              </div>
              <div class="form-field">
                <label>Date of Birth (mm/dd/yyyy):</label>
                <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
              </div>
              <div class="form-field">
                <label>Place of Birth:</label>
                <input type="text" name="place_of_birth" class="form-control" value="{{ old('place_of_birth') }}" required>
              </div>
              <div class="form-field">
                <label>Sex:</label>
                <select name="gender" class="form-control" required>
                  <option value="">Select</option>
                  <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                  <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                  <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
              <div class="form-field">
                <label>Civil Status:</label>
                <select name="civil_status" class="form-control" required>
                  <option value="">Select</option>
                  <option value="single" {{ old('civil_status') == 'single' ? 'selected' : '' }}>Single</option>
                  <option value="married" {{ old('civil_status') == 'married' ? 'selected' : '' }}>Married</option>
                  <option value="divorced" {{ old('civil_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                  <option value="widowed" {{ old('civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                </select>
              </div>
              <div class="form-field">
                <label>Nationality:</label>
                <input type="text" name="nationality" class="form-control" value="{{ old('nationality') }}">
              </div>
            </div>
          </div>

          <!-- Section D: Other -->
          <div class="form-section">
            <div class="section-label">D. Other:</div>
            <div class="section-row">
              <div class="form-field">
                <label>Religion:</label>
                <input type="text" name="religion" class="form-control" value="{{ old('religion') }}">
              </div>
              <div class="form-field">
                <label>Mobile No.:</label>
                <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" required>
              </div>
              <div class="form-field">
                <label>Tel No.:</label>
                <input type="text" name="tel_no" class="form-control" value="{{ old('tel_no') }}">
              </div>
              <div class="form-field">
                <label>Email Address:</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
              </div>
            </div>
            <div class="section-row" id="spouse-section" style="display: none;">
              <div class="form-field">
                <label>If Married, Name of Spouse:</label>
                <input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name') }}">
              </div>
              <div class="form-field">
                <label>Spouse Contact No.:</label>
                <input type="text" name="spouse_contact_no" class="form-control" value="{{ old('spouse_contact_no') }}">
              </div>
            </div>
          </div>

          <!-- Section E: SPECIAL SKILLS AND TALENTS -->
          <div class="form-section">
            <div class="section-label">E. SPECIAL SKILLS AND TALENTS</div>
            <div class="section-row">
              <div class="form-field">
                <label>Sport:</label>
                <input type="text" name="sport" class="form-control" value="{{ old('sport') }}">
              </div>
              <div class="form-field">
                <label>Arts:</label>
                <input type="text" name="arts" class="form-control" value="{{ old('arts') }}">
              </div>
              <div class="form-field">
                <label>Technical:</label>
                <input type="text" name="technical" class="form-control" value="{{ old('technical') }}">
              </div>
            </div>
          </div>

          <!-- Section F: EDUCATION BACKGROUND -->
          <div class="form-section">
            <div class="section-label">F. EDUCATION BACKGROUND</div>
            <div class="mb-3">
              <strong>Junior High School / High School:</strong>
              <div class="section-row">
                <div class="form-field form-field-full">
                  <label>Junior High School / High School:</label>
                  <input type="text" name="junior_high_school_name" class="form-control" value="{{ old('junior_high_school_name') }}" placeholder="Junior High School / High School">
                </div>
                <div class="form-field">
                  <label>Year Completed/Graduated:</label>
                  <input type="text" name="junior_high_school_year_completed" class="form-control" value="{{ old('junior_high_school_year_completed') }}" placeholder="Year Completed/Graduated">
                </div>
                <div class="form-field form-field-full">
                  <label>Complete School Address:</label>
                  <textarea name="junior_high_school_address" class="form-control" rows="2" placeholder="Complete School Address">{{ old('junior_high_school_address') }}</textarea>
                </div>
                <div class="form-field">
                  <label>Honors/Awards:</label>
                  <input type="text" name="junior_high_school_honors_awards" class="form-control" value="{{ old('junior_high_school_honors_awards') }}" placeholder="Honors/Awards">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <strong>Senior High School:</strong>
              <div class="section-row">
                <div class="form-field">
                  <label>Senior High School:</label>
                  <input type="text" name="senior_high_school_name" class="form-control" value="{{ old('senior_high_school_name') }}" placeholder="Senior High School">
                </div>
                <div class="form-field">
                  <label>Year Graduated:</label>
                  <input type="text" name="senior_high_school_year_graduated" class="form-control" value="{{ old('senior_high_school_year_graduated') }}" placeholder="Year Graduated">
                </div>
                <div class="form-field">
                  <label>Track and Strand:</label>
                  <input type="text" name="senior_high_school_track_strand" class="form-control" value="{{ old('senior_high_school_track_strand') }}" placeholder="Track and Strand">
                </div>
                <div class="form-field">
                  <label>LRN:</label>
                  <input type="text" name="senior_high_school_lrn" class="form-control" value="{{ old('senior_high_school_lrn') }}" placeholder="LRN">
                </div>
                <div class="form-field form-field-full">
                  <label>Complete School Address:</label>
                  <textarea name="senior_high_school_address" class="form-control" rows="2" placeholder="Complete School Address">{{ old('senior_high_school_address') }}</textarea>
                </div>
                <div class="form-field">
                  <label>Honor/Awards:</label>
                  <input type="text" name="senior_high_school_honors_awards" class="form-control" value="{{ old('senior_high_school_honors_awards') }}" placeholder="Honor/Awards">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <strong>If TRANSFEREE/ SECOND COURSER, please indicate necessary details.</strong>
              <div class="section-row">
                <div class="form-field">
                  <label>Last School Attended:</label>
                  <input type="text" name="last_school_attended" class="form-control" value="{{ old('last_school_attended') }}">
                </div>
                <div class="form-field">
                  <label>Course:</label>
                  <input type="text" name="last_school_course" class="form-control" value="{{ old('last_school_course') }}">
                </div>
                <div class="form-field form-field-full">
                  <label>Complete Address:</label>
                  <textarea name="last_school_address" class="form-control" rows="2">{{ old('last_school_address') }}</textarea>
                </div>
                <div class="form-field">
                  <label>Last School Year Attended:</label>
                  <input type="text" name="last_school_year_attended" class="form-control" value="{{ old('last_school_year_attended') }}">
                </div>
              </div>
            </div>
          </div>

          <!-- Section G: FAMILY BACKGROUND -->
          <div class="form-section">
            <div class="section-label">G. FAMILY BACKGROUND</div>
            <div class="mb-3">
              <strong>Father's Information:</strong>
              <div class="section-row">
                <div class="form-field">
                  <label>Father's Name:</label>
                  <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}">
                </div>
                <div class="form-field">
                  <label>Contact Number:</label>
                  <input type="text" name="father_contact_number" class="form-control" value="{{ old('father_contact_number') }}">
                </div>
                <div class="form-field">
                  <label>Occupation:</label>
                  <input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation') }}">
                </div>
                <div class="form-field">
                  <label>Name of workplace:</label>
                  <input type="text" name="father_workplace" class="form-control" value="{{ old('father_workplace') }}">
                </div>
                <div class="form-field">
                  <label>Father's Monthly Income:</label>
                  <input type="text" name="father_monthly_income" class="form-control" value="{{ old('father_monthly_income') }}">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <strong>Mother's Information:</strong>
              <div class="section-row">
                <div class="form-field">
                  <label>Mother's Name:</label>
                  <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}">
                </div>
                <div class="form-field">
                  <label>Contact No.:</label>
                  <input type="text" name="mother_contact_number" class="form-control" value="{{ old('mother_contact_number') }}">
                </div>
                <div class="form-field">
                  <label>Occupation:</label>
                  <input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation') }}">
                </div>
                <div class="form-field">
                  <label>Name of Workplace:</label>
                  <input type="text" name="mother_workplace" class="form-control" value="{{ old('mother_workplace') }}">
                </div>
                <div class="form-field">
                  <label>Mother's Monthly Income:</label>
                  <input type="text" name="mother_monthly_income" class="form-control" value="{{ old('mother_monthly_income') }}">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <strong>Guardian's Information (Skip this section if you are currently living with your parents):</strong>
              <div class="section-row">
                <div class="form-field">
                  <label>Guardian's Name:</label>
                  <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}">
                </div>
                <div class="form-field">
                  <label>Relationship:</label>
                  <input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship') }}">
                </div>
                <div class="form-field">
                  <label>Contact Number:</label>
                  <input type="text" name="guardian_contact_number" class="form-control" value="{{ old('guardian_contact_number') }}">
                </div>
                <div class="form-field">
                  <label>Occupation:</label>
                  <input type="text" name="guardian_occupation" class="form-control" value="{{ old('guardian_occupation') }}">
                </div>
                <div class="form-field">
                  <label>Name of workplace:</label>
                  <input type="text" name="guardian_workplace" class="form-control" value="{{ old('guardian_workplace') }}">
                </div>
                <div class="form-field">
                  <label>Guardian's Monthly Income:</label>
                  <input type="text" name="guardian_monthly_income" class="form-control" value="{{ old('guardian_monthly_income') }}">
                </div>
              </div>
            </div>
          </div>

          <!-- Section H: OTHER INFORMATION -->
          <div class="form-section">
            <div class="section-label">H. OTHER INFORMATION</div>
            <div class="section-row">
              <div class="form-field">
                <label>Are you a an active scholar for this semester?</label>
                <div style="display: flex; gap: 15px; margin-top: 5px;">
                  <label style="font-weight: normal;">
                    <input type="radio" name="is_active_scholar" value="1" {{ old('is_active_scholar') == '1' ? 'checked' : '' }}> Yes
                  </label>
                  <label style="font-weight: normal;">
                    <input type="radio" name="is_active_scholar" value="0" {{ old('is_active_scholar') == '0' || old('is_active_scholar') == null ? 'checked' : '' }}> No
                  </label>
                </div>
              </div>
              <div class="form-field">
                <label>If you have a scholarship, kindly indicate the name of the Scholarship Grant:</label>
                <input type="text" name="scholarship_grant_name" class="form-control" value="{{ old('scholarship_grant_name') }}">
              </div>
            </div>
            <div class="section-row">
              <div class="form-field">
                <label>Are you a part of an Indigenous Group:</label>
                <div style="display: flex; gap: 15px; margin-top: 5px;">
                  <label style="font-weight: normal;">
                    <input type="radio" name="is_indigenous_group_member" value="1" {{ old('is_indigenous_group_member') == '1' ? 'checked' : '' }}> Yes
                  </label>
                  <label style="font-weight: normal;">
                    <input type="radio" name="is_indigenous_group_member" value="0" {{ old('is_indigenous_group_member') == '0' || old('is_indigenous_group_member') == null ? 'checked' : '' }}> No
                  </label>
                </div>
              </div>
              <div class="form-field">
                <label>If you are a part of an Indigenous group, please specify:</label>
                <input type="text" name="indigenous_group_specify" class="form-control" value="{{ old('indigenous_group_specify') }}">
              </div>
            </div>
            <div class="section-row">
              <div class="form-field">
                <label>Are you a Person with Disability (PWD)?</label>
                <small class="text-muted d-block">(Only those with an official PWD ID are considered for this category.)</small>
                <div style="display: flex; gap: 15px; margin-top: 5px;">
                  <label style="font-weight: normal;">
                    <input type="radio" name="is_pwd" value="1" {{ old('is_pwd') == '1' ? 'checked' : '' }}> Yes
                  </label>
                  <label style="font-weight: normal;">
                    <input type="radio" name="is_pwd" value="0" {{ old('is_pwd') == '0' || old('is_pwd') == null ? 'checked' : '' }}> No
                  </label>
                </div>
              </div>
              <div class="form-field">
                <label>If you are a PWD, kindly upload your valid ID here:</label>
                <input type="file" name="pwd_id_image" class="form-control" accept="image/*">
                <small class="text-muted">Upload 1 supported file: image. Max 100 MB.</small>
              </div>
            </div>
            <div class="section-row">
              <div class="form-field">
                <label>Are you a member of any government or political organization?</label>
                <small class="text-muted d-block">(Please check the option that applies to you. Examples include Sangguniang Kabataan (SK), Barangay, Municipal, City, Provincial, or National government positions or organizations.)</small>
                <select name="is_government_member" class="form-control" id="is-government-member">
                  <option value="no" {{ old('is_government_member') == 'no' || old('is_government_member') == null ? 'selected' : '' }}>No – I am not a member of any government or political organization.</option>
                  <option value="yes" {{ old('is_government_member') == 'yes' ? 'selected' : '' }}>Yes – I am currently involved</option>
                </select>
              </div>
            </div>
            <div class="section-row">
              <div class="form-field" id="government-level-field" style="display: none;">
                <label>If you are a government official, kindly specify the level:</label>
                <select name="government_level" class="form-control">
                  <option value="">Select option:</option>
                  <option value="barangay" {{ old('government_level') == 'barangay' ? 'selected' : '' }}>Barangay Government</option>
                  <option value="municipal_city" {{ old('government_level') == 'municipal_city' ? 'selected' : '' }}>Municipal/City Government</option>
                  <option value="provincial" {{ old('government_level') == 'provincial' ? 'selected' : '' }}>Provincial Government</option>
                </select>
              </div>
              <div class="form-field" id="government-role-field" style="display: none;">
                <label>If you are a government official, indicate your role or position:</label>
                <small class="text-muted d-block">(For example: SK Chairperson, SK Councilor, Barangay Secretary, Barangay Treasurer, Municipal Youth Representative, City Council Staff, or any official role in a government office or organization.)</small>
                <input type="text" name="government_role_position" class="form-control" value="{{ old('government_role_position') }}" placeholder="Your role or position">
              </div>
            </div>
            <div class="section-row">
              <div class="form-field">
                <label>Current living arrangement:</label>
                <select name="living_arrangement" class="form-control" id="living-arrangement">
                  <option value="">Select</option>
                  <option value="home" {{ old('living_arrangement') == 'home' ? 'selected' : '' }}>I live at home – with my parents or immediate family</option>
                  <option value="boarding_house" {{ old('living_arrangement') == 'boarding_house' ? 'selected' : '' }}>I live in a boarding house – renting a room or space near the campus</option>
                  <option value="relatives" {{ old('living_arrangement') == 'relatives' ? 'selected' : '' }}>I live with relatives – staying with extended family members</option>
                  <option value="working_student" {{ old('living_arrangement') == 'working_student' ? 'selected' : '' }}>I live as a working student – employed while studying and living independently</option>
                  <option value="others" {{ old('living_arrangement') == 'others' ? 'selected' : '' }}>Others (please specify)</option>
                </select>
              </div>
              <div class="form-field" id="living-arrangement-others-field" style="display: none;">
                <label>Others (please specify):</label>
                <input type="text" name="living_arrangement_others_specify" class="form-control" value="{{ old('living_arrangement_others_specify') }}">
              </div>
            </div>
            <div class="section-row">
              <div class="form-field">
                <label>Are you a single parent?</label>
                <div style="display: flex; gap: 15px; margin-top: 5px;">
                  <label style="font-weight: normal;">
                    <input type="radio" name="is_single_parent" value="1" {{ old('is_single_parent') == '1' ? 'checked' : '' }}> YES
                  </label>
                  <label style="font-weight: normal;">
                    <input type="radio" name="is_single_parent" value="0" {{ old('is_single_parent') == '0' || old('is_single_parent') == null ? 'checked' : '' }}> NO
                  </label>
                </div>
              </div>
              <div class="form-field">
                <label>Are you a member of a fraternity /or Sorority?</label>
                <small class="text-muted d-block">(Please indicate the name and your position if applicable)</small>
                <input type="text" name="fraternity_sorority_name" class="form-control" value="{{ old('fraternity_sorority_name') }}" placeholder="Name of Fraternity/Sorority">
              </div>
              <div class="form-field">
                <label>Position in Fraternity/Sorority:</label>
                <input type="text" name="fraternity_sorority_position" class="form-control" value="{{ old('fraternity_sorority_position') }}" placeholder="Your position (if applicable)">
              </div>
            </div>
            <div class="section-row">
              <div class="form-field">
                <label>Did you have any previous criminal offense/record?</label>
                <div style="display: flex; gap: 15px; margin-top: 5px;">
                  <label style="font-weight: normal;">
                    <input type="radio" name="has_criminal_record" value="1" {{ old('has_criminal_record') == '1' ? 'checked' : '' }}> YES
                  </label>
                  <label style="font-weight: normal;">
                    <input type="radio" name="has_criminal_record" value="0" {{ old('has_criminal_record') == '0' || old('has_criminal_record') == null ? 'checked' : '' }}> NO
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Section I: Degree Applied/Enrolled -->
          <div class="form-section">
            <div class="section-label">I. Degree Applied/Enrolled (State in Full)</div>
            <div class="section-row">
              <div class="form-field">
                <label>Department</label>
                <select name="department_id" class="form-control" id="department-select" required>
                  <option value="">Select Department</option>
                  @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-field">
                <label>Course</label>
                <select name="course_id" class="form-control" id="course-select" required>
                  <option value="">Select Course</option>
                  @foreach($courses as $course)
                    <option value="{{ $course->id }}" data-department="{{ $course->department_id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-field">
                <label>Year Level</label>
                <input type="number" name="year_level" class="form-control" value="{{ old('year_level') }}" min="1" max="10" placeholder="Year Level" required>
              </div>
            </div>
            <div class="section-row">
              <div class="form-field">
                <label>Organization</label>
                <select name="organization_id" class="form-control" id="organization-select">
                  <option value="">Select Organization</option>
                  @foreach($organizations as $org)
                    <option value="{{ $org->id }}" data-department="{{ $org->department_id }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-field">
                <label>Student Type 1</label>
                <select name="student_type1" class="form-control" required>
                  <option value="">Select Type</option>
                  <option value="regular" {{ old('student_type1') == 'regular' ? 'selected' : '' }}>Regular</option>
                  <option value="irregular" {{ old('student_type1') == 'irregular' ? 'selected' : '' }}>Irregular</option>
                  <option value="transferee" {{ old('student_type1') == 'transferee' ? 'selected' : '' }}>Transferee</option>
                </select>
              </div>
              <div class="form-field">
                <label>Student Type 2</label>
                <select name="student_type2" class="form-control" id="student-type2-select" required>
                  <option value="">Select Type</option>
                  <option value="paying" {{ old('student_type2') == 'paying' ? 'selected' : '' }}>Paying</option>
                  <option value="scholar" {{ old('student_type2') == 'scholar' ? 'selected' : '' }}>Scholar</option>
                </select>
              </div>
            </div>
            <div class="section-row">
              <div class="form-field">
                <label>Scholarship</label>
                <select name="scholarship_id" class="form-control" id="scholarship-select" disabled>
                  <option value="">Select Scholarship</option>
                  @foreach($scholarships as $scholarship)
                    <option value="{{ $scholarship->id }}" {{ old('scholarship_id') == $scholarship->id ? 'selected' : '' }}>{{ $scholarship->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <!-- Section J: Entrance Credential -->
          <div class="form-section">
            <div class="section-label">J. Entrance Credential</div>
            <div class="mb-2"><strong>Presented:</strong></div>
            <div class="checkbox-group">
              <div class="checkbox-item">
                <input type="checkbox" name="form_137_presented" id="form_137" value="1" {{ old('form_137_presented') ? 'checked' : '' }}>
                <label for="form_137" style="font-weight: normal; margin: 0;">Form 137</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="tor_presented" id="tor" value="1" {{ old('tor_presented') ? 'checked' : '' }}>
                <label for="tor" style="font-weight: normal; margin: 0;">TOR</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="good_moral_cert_presented" id="good_moral" value="1" {{ old('good_moral_cert_presented') ? 'checked' : '' }}>
                <label for="good_moral" style="font-weight: normal; margin: 0;">Good Moral Cert.</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="birth_cert_presented" id="birth_cert" value="1" {{ old('birth_cert_presented') ? 'checked' : '' }}>
                <label for="birth_cert" style="font-weight: normal; margin: 0;">Birth Cert.</label>
              </div>
              <div class="checkbox-item">
                <input type="checkbox" name="marriage_cert_presented" id="marriage_cert" value="1" {{ old('marriage_cert_presented') ? 'checked' : '' }}>
                <label for="marriage_cert" style="font-weight: normal; margin: 0;">Marriage Cert.</label>
              </div>
            </div>
          </div>

          <div class="text-center mt-4 mb-3">
            <button type="submit" class="btn btn-primary btn-lg">Submit Student Information Sheet</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Department auto-fill functionality
    const quickDeptSelect = document.getElementById('quick-department-select');
    const fillDeptBtn = document.getElementById('fill-department-btn');
    const clearDeptBtn = document.getElementById('clear-department-btn');
    const deptInfo = document.getElementById('department-info');
    const selectedDeptName = document.getElementById('selected-dept-name');
    const selectedOrgName = document.getElementById('selected-org-name');
    const departmentSelect = document.getElementById('department-select');
    const courseSelect = document.getElementById('course-select');
    const organizationSelect = document.getElementById('organization-select');
    
    // Organizations data for auto-fill
    const organizations = @json($organizations);
    
    // Enable/disable fill button based on department selection
    if (quickDeptSelect) {
      quickDeptSelect.addEventListener('change', function() {
        if (this.value) {
          fillDeptBtn.disabled = false;
          const selectedOption = this.options[this.selectedIndex];
          const deptName = selectedOption.getAttribute('data-dept-name');
          selectedDeptName.textContent = deptName;
          
          // Find department-related organization
          const deptId = parseInt(this.value);
          const deptOrg = organizations.find(org => org.department_id == deptId);
          if (deptOrg) {
            selectedOrgName.textContent = deptOrg.name;
          } else {
            selectedOrgName.textContent = 'No department-related organization';
          }
          deptInfo.style.display = 'block';
        } else {
          fillDeptBtn.disabled = true;
          deptInfo.style.display = 'none';
        }
      });
      
      // Auto-fill form when button is clicked
      fillDeptBtn.addEventListener('click', async function() {
        const deptId = quickDeptSelect.value;
        if (deptId && departmentSelect) {
          // Set department
          departmentSelect.value = deptId;
          
          // Load courses and organizations dynamically
          await Promise.all([
            loadCourses(deptId),
            loadOrganizations(deptId)
          ]);
          
          // Find and set department-related organization after loading
          const deptOrg = organizations.find(org => org.department_id == deptId);
          if (deptOrg && organizationSelect) {
            organizationSelect.value = deptOrg.id;
          }
          
          // Show clear button
          fillDeptBtn.style.display = 'none';
          clearDeptBtn.style.display = 'inline-block';
          
          // Scroll to form
          document.querySelector('form').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
      
      // Clear selection
      clearDeptBtn.addEventListener('click', async function() {
        quickDeptSelect.value = '';
        fillDeptBtn.disabled = true;
        fillDeptBtn.style.display = 'inline-block';
        clearDeptBtn.style.display = 'none';
        deptInfo.style.display = 'none';
        if (departmentSelect) {
          departmentSelect.value = '';
          // Reset courses and organizations
          await Promise.all([
            loadCourses(''),
            loadOrganizations(null)
          ]);
        }
        if (organizationSelect) {
          organizationSelect.value = '';
        }
        if (courseSelect) {
          courseSelect.value = '';
        }
      });
    }
    
    // Image preview functionality
    const imageInput = document.getElementById('personal-data-sheet-image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (imageInput && imagePreview && previewImg) {
      imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImg.src = e.target.result;
            imagePreview.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else {
          imagePreview.style.display = 'none';
        }
      });
    }

    // Dynamic loading of courses and organizations via AJAX
    const baseUrl = "{{ url('/') }}";
    const oldCourseId = "{{ old('course_id', '') }}";
    const oldOrganizationId = "{{ old('organization_id', '') }}";
    
    // Load courses dynamically based on department
    async function loadCourses(departmentId) {
      if (!courseSelect) return;
      
      // Show loading state
      courseSelect.disabled = true;
      courseSelect.innerHTML = '<option value="">Loading courses...</option>';
      
      if (!departmentId) {
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = false;
        return;
      }
      
      try {
        const response = await fetch(`${baseUrl}/api/courses/${departmentId}`);
        if (!response.ok) throw new Error('Failed to load courses');
        
        const courses = await response.json();
        
        // Clear and populate course dropdown
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courses.forEach(course => {
          const option = document.createElement('option');
          option.value = course.id;
          option.textContent = course.name;
          option.setAttribute('data-department', course.department_id);
          
          // Preserve old value if it matches
          if (oldCourseId && oldCourseId == course.id) {
            option.selected = true;
          }
          
          courseSelect.appendChild(option);
        });
        
        courseSelect.disabled = false;
      } catch (error) {
        console.error('Error loading courses:', error);
        courseSelect.innerHTML = '<option value="">Error loading courses</option>';
        courseSelect.disabled = false;
      }
    }
    
    // Load organizations dynamically based on department
    async function loadOrganizations(departmentId) {
      if (!organizationSelect) return;
      
      // Show loading state
      organizationSelect.disabled = true;
      organizationSelect.innerHTML = '<option value="">Loading organizations...</option>';
      
      try {
        let url = `${baseUrl}/api/organizations`;
        if (departmentId) {
          url += `?department_id=${encodeURIComponent(departmentId)}`;
        }
        
        const response = await fetch(url);
        if (!response.ok) throw new Error('Failed to load organizations');
        
        const orgs = await response.json();
        
        // Filter organizations by department if department is selected
        let filteredOrgs = orgs;
        if (departmentId) {
          filteredOrgs = orgs.filter(org => 
            !org.department_id || org.department_id == departmentId
          );
        }
        
        // Clear and populate organization dropdown
        organizationSelect.innerHTML = '<option value="">Select Organization</option>';
        filteredOrgs.forEach(org => {
          const option = document.createElement('option');
          option.value = org.id;
          option.textContent = org.name;
          if (org.department_id) {
            option.setAttribute('data-department', org.department_id);
          }
          
          // Preserve old value if it matches
          if (oldOrganizationId && oldOrganizationId == org.id) {
            option.selected = true;
          }
          
          organizationSelect.appendChild(option);
        });
        
        organizationSelect.disabled = false;
      } catch (error) {
        console.error('Error loading organizations:', error);
        organizationSelect.innerHTML = '<option value="">Error loading organizations</option>';
        organizationSelect.disabled = false;
      }
    }
    
    // Handle department change - dynamically load courses and organizations
    function handleDepartmentChange() {
      const deptId = departmentSelect ? departmentSelect.value : '';
      
      // Load courses and organizations dynamically
      loadCourses(deptId);
      loadOrganizations(deptId);
    }
    
    // Get old department ID from PHP
    const oldDepartmentId = "{{ old('department_id', '') }}";
    
    // Initialize on page load
    if (departmentSelect) {
      // Load courses and organizations based on initial department selection
      const initialDeptId = departmentSelect.value || oldDepartmentId;
      if (initialDeptId) {
        if (!departmentSelect.value) {
          departmentSelect.value = initialDeptId;
        }
        loadCourses(initialDeptId);
        loadOrganizations(initialDeptId);
      } else {
        // If no department selected, show all organizations
        loadOrganizations(null);
      }
      
      // Listen for department changes
      departmentSelect.addEventListener('change', handleDepartmentChange);
    }

    // Calculate age from birth date
    const birthDateInput = document.querySelector('input[name="birth_date"]');
    const ageInput = document.querySelector('input[name="age"]');
    if (birthDateInput && ageInput) {
      birthDateInput.addEventListener('change', function() {
        if (this.value) {
          const birthDate = new Date(this.value);
          const today = new Date();
          let age = today.getFullYear() - birthDate.getFullYear();
          const monthDiff = today.getMonth() - birthDate.getMonth();
          if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
          }
          ageInput.value = age > 0 ? age : '';
        }
      });
    }

    // Convert all text inputs and textareas to uppercase automatically
    const textInputs = document.querySelectorAll('input[type="text"], textarea');
    textInputs.forEach(input => {
      // Skip email, number, and date fields
      if (input.type === 'email' || input.type === 'number' || input.type === 'date') {
        return;
      }
      
      // Add style for visual uppercase display
      input.style.textTransform = 'uppercase';
      
      // Convert to uppercase on input
      input.addEventListener('input', function() {
        const cursorPosition = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(cursorPosition, cursorPosition);
      });
      
      // Convert to uppercase on paste
      input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text').toUpperCase();
        const start = this.selectionStart;
        const end = this.selectionEnd;
        this.value = this.value.substring(0, start) + pastedText + this.value.substring(end);
        const newCursorPosition = start + pastedText.length;
        this.setSelectionRange(newCursorPosition, newCursorPosition);
      });
      
      // Convert existing value to uppercase
      if (input.value) {
        input.value = input.value.toUpperCase();
      }
    });

    // Enter key navigation - move to next visible field, don't skip any
    const form = document.querySelector('form');
    if (form) {
      // Function to get all visible focusable fields in order
      function getVisibleFocusableFields() {
        return Array.from(form.querySelectorAll(
          'input:not([type="radio"]):not([type="checkbox"]):not([type="submit"]):not([type="button"]):not([type="hidden"]):not([disabled]), ' +
          'select:not([disabled]), textarea:not([disabled])'
        )).filter(field => {
          // Check if field is visible (not hidden by display:none)
          const style = window.getComputedStyle(field);
          if (style.display === 'none') return false;
          
          // Check if parent section is visible
          let parent = field;
          while (parent && parent !== form) {
            const parentStyle = window.getComputedStyle(parent);
            if (parentStyle.display === 'none') return false;
            parent = parent.parentElement;
          }
          
          return true;
        });
      }
      // Use event delegation to handle dynamically shown/hidden fields
      form.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          const target = e.target;
          
          // Only handle Enter on input, select, and textarea fields
          if (!['INPUT', 'SELECT', 'TEXTAREA'].includes(target.tagName)) {
            return;
          }
          
          // Skip if it's a radio, checkbox, submit, button, or hidden field
          if (target.type === 'radio' || target.type === 'checkbox' || 
              target.type === 'submit' || target.type === 'button' || 
              target.type === 'hidden' || target.disabled) {
            return;
          }
          
          e.preventDefault();
          
          // Get all currently visible focusable fields
          const focusableFields = getVisibleFocusableFields();
          const currentIndex = focusableFields.indexOf(target);
          
          // Find next visible focusable field
          const nextIndex = currentIndex + 1;
          if (nextIndex < focusableFields.length) {
            // Focus on next field
            const nextField = focusableFields[nextIndex];
            nextField.focus();
            
            // For select fields, open dropdown
            if (nextField.tagName === 'SELECT') {
              setTimeout(() => {
                nextField.focus();
              }, 10);
            }
          } else {
            // Last field - submit form
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
              submitButton.click();
            } else {
              form.submit();
            }
          }
        }
      });
    }
    
    // Declare civil status select once at the top level
    const civilStatusSelect = document.querySelector('select[name="civil_status"]');
    const genderSelect = document.querySelector('select[name="gender"]');
    
    // Show/hide Section B based on Civil Status and Gender
    function toggleSectionB() {
      const sectionB = document.getElementById('section-b');
      const maidenNameInput = document.querySelector('input[name="maiden_name"]');
      
      if (genderSelect && civilStatusSelect && sectionB) {
        const gender = genderSelect.value;
        const civilStatus = civilStatusSelect.value;
        
        // Hide Section B if gender is "male" OR civil status is "single"
        if (gender === 'male' || civilStatus === 'single') {
          sectionB.style.display = 'none';
          if (maidenNameInput) {
            maidenNameInput.value = '';
          }
        } else {
          sectionB.style.display = 'block';
        }
      }
    }
    
    // Listen for changes in Gender and Civil Status
    if (genderSelect) {
      genderSelect.addEventListener('change', toggleSectionB);
    }
    if (civilStatusSelect) {
      civilStatusSelect.addEventListener('change', toggleSectionB);
    }
    
    // Initial check on page load
    toggleSectionB();
    
    // Toggle Scholarship dropdown based on Student Type 2
    const studentType2Select = document.getElementById('student-type2-select');
    const scholarshipSelect = document.getElementById('scholarship-select');
    
    function toggleScholarship() {
      if (studentType2Select && scholarshipSelect) {
        if (studentType2Select.value === 'scholar') {
          scholarshipSelect.disabled = false;
          scholarshipSelect.style.opacity = '1';
        } else {
          scholarshipSelect.disabled = true;
          scholarshipSelect.style.opacity = '0.5';
          scholarshipSelect.value = ''; // Clear selection when disabled
        }
      }
    }
    
    if (studentType2Select) {
      studentType2Select.addEventListener('change', toggleScholarship);
      // Initial check
      toggleScholarship();
    }
    
    // Toggle spouse section based on civil status
    const spouseSection = document.getElementById('spouse-section');
    
    function toggleSpouseSection() {
      if (civilStatusSelect && spouseSection) {
        if (civilStatusSelect.value === 'married') {
          spouseSection.style.display = 'flex';
        } else {
          spouseSection.style.display = 'none';
        }
      }
    }
    
    if (civilStatusSelect) {
      civilStatusSelect.addEventListener('change', toggleSpouseSection);
      toggleSpouseSection();
    }
    
    // Toggle government member fields
    const isGovernmentMember = document.getElementById('is-government-member');
    const governmentLevelField = document.getElementById('government-level-field');
    const governmentRoleField = document.getElementById('government-role-field');
    
    function toggleGovernmentFields() {
      if (isGovernmentMember && governmentLevelField && governmentRoleField) {
        if (isGovernmentMember.value === 'yes') {
          governmentLevelField.style.display = 'block';
          governmentRoleField.style.display = 'block';
        } else {
          governmentLevelField.style.display = 'none';
          governmentRoleField.style.display = 'none';
        }
      }
    }
    
    if (isGovernmentMember) {
      isGovernmentMember.addEventListener('change', toggleGovernmentFields);
      toggleGovernmentFields();
    }
    
    // Toggle living arrangement others field
    const livingArrangement = document.getElementById('living-arrangement');
    const livingArrangementOthersField = document.getElementById('living-arrangement-others-field');
    
    function toggleLivingArrangementOthers() {
      if (livingArrangement && livingArrangementOthersField) {
        if (livingArrangement.value === 'others') {
          livingArrangementOthersField.style.display = 'block';
        } else {
          livingArrangementOthersField.style.display = 'none';
        }
      }
    }
    
    if (livingArrangement) {
      livingArrangement.addEventListener('change', toggleLivingArrangementOthers);
      toggleLivingArrangementOthers();
    }
  });
</script>
@endsection
