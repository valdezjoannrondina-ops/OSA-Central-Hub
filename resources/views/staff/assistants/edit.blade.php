@extends('layouts.app')

@section('title', 'Edit Assistant')

@section('content')
<div class="container-fluid">
  <div class="row">
    @include('staff.partials.sidebar')
    <main class="col-md-10">
      <h3 class="mt-4"><span class="d-block w-100 px-3 py-2" style="background-color: midnightblue; color: white; border-radius: 4px;">Edit Assistant</span></h3>
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <form method="POST" action="{{ route('staff.assistants.update', $assistant->id) }}" enctype="multipart/form-data" class="card p-4 mb-4">
        @csrf
        @method('PUT')
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td colspan="2">
                <div class="row g-2">
                  <div class="col-md-4">
                    <input type="text" name="user_id" id="user_id" class="form-control" placeholder="Student ID" value="{{ old('user_id', $assistant->user_id) }}" required>
                    <small class="text-muted">Student ID</small>
                  </div>
                  <div class="col-md-4">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="{{ old('email', $assistant->email) }}" required>
                    <small class="text-muted">Email</small>
                  </div>
                  <div class="col-md-4">
                    <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="Contact" value="{{ old('contact_number', $assistant->contact_number) }}">
                    <small class="text-muted">Contact Number</small>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
                    <div class="col-md-6" data-birth-age-pair>
                      @include('components.birthdate_with_age', ['name' => 'birth_date', 'ageName' => 'age', 'value' => old('birth_date', optional($assistant->birth_date)->format('m/d/Y'))])
                      <small class="text-muted">Birth Date</small>
                    </div>
                    <div class="col-md-6">
                      <select name="gender" id="gender" class="form-select">
                        @php $g = old('gender', $assistant->gender); @endphp
                        <option value="">Select Gender</option>
                        <option value="male" {{ $g=='male'?'selected':'' }}>Male</option>
                        <option value="female" {{ $g=='female'?'selected':'' }}>Female</option>
                        <option value="other" {{ $g=='other'?'selected':'' }}>Other</option>
                      </select>
                      <small class="text-muted">Gender</small>
                    </div>
              <td colspan="2">
                <div class="row g-2">
                  <div class="col-md-4">
              <tr>
                <td colspan="2">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <select name="course_id" id="course_id" class="form-select">
                        <option value="">Select Course</option>
                        @php $cid = old('course_id', $assistant->course_id); @endphp
                        @foreach(\App\Models\Course::where('department_id', old('department_id', $assistant->department_id))->orderBy('name')->get() as $c)
                          <option value="{{ $c->id }}" {{ $cid==$c->id?'selected':'' }}>{{ $c->name }}</option>
                        @endforeach
                      </select>
                      <small class="text-muted">Course</small>
                    </div>
                    <div class="col-md-4">
                      <input type="number" name="year_level" id="year_level" class="form-control" min="1" max="10" placeholder="Year Level" value="{{ old('year_level', $assistant->year_level) }}">
                      <small class="text-muted">Year Level</small>
                    </div>
                    <div class="col-md-4">
                      <select name="scholarship_id" id="scholarship_id" class="form-select">
                        <option value="">Select Scholarship</option>
                        @php $sid = old('scholarship_id', $assistant->scholarship_id); @endphp
                        @foreach(\App\Models\Scholarship::orderBy('name')->get() as $s)
                          <option value="{{ $s->id }}" {{ $sid==$s->id?'selected':'' }}>{{ $s->name }}</option>
                        @endforeach
                      </select>
                      <small class="text-muted">Scholarship</small>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="row g-2">
                    <div class="col-md-4">
                      @php $t1 = old('student_type1', $assistant->student_type1); @endphp
                      <select name="student_type1" id="student_type1" class="form-select">
                        <option value="">Type 1</option>
                        <option value="regular" {{ $t1=='regular'?'selected':'' }}>Regular</option>
                        <option value="irregular" {{ $t1=='irregular'?'selected':'' }}>Irregular</option>
                        <option value="transferee" {{ $t1=='transferee'?'selected':'' }}>Transferee</option>
                      </select>
                      <small class="text-muted">Student Type 1</small>
                    </div>
                    <div class="col-md-4">
                      @php $t2 = old('student_type2', $assistant->student_type2); @endphp
                      <select name="student_type2" id="student_type2" class="form-select">
                        <option value="">Type 2</option>
                        <option value="paying" {{ $t2=='paying'?'selected':'' }}>Paying</option>
                        <option value="scholar" {{ $t2=='scholar'?'selected':'' }}>Scholar</option>
                      </select>
                      <small class="text-muted">Student Type 2</small>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="row g-2">
                    <div class="col-md-4">
                      <input type="text" name="emergency_contact_name" class="form-control" placeholder="Emergency Contact Name" value="{{ old('emergency_contact_name', $assistant->emergency_contact_name) }}">
                      <small class="text-muted">Emergency Contact Name</small>
                    </div>
                    <div class="col-md-4">
                      <input type="text" name="emergency_contact_number" class="form-control" placeholder="Emergency Contact Number" value="{{ old('emergency_contact_number', $assistant->emergency_contact_number) }}">
                      <small class="text-muted">Emergency Contact Number</small>
                    </div>
                    <div class="col-md-4">
                      <input type="text" name="emergency_relation" class="form-control" placeholder="Emergency Relation" value="{{ old('emergency_relation', $assistant->emergency_relation) }}">
                      <small class="text-muted">Emergency Relation</small>
                    </div>
                  </div>
                </td>
              </tr>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First" value="{{ old('first_name', $assistant->first_name) }}" required>
                    <small class="text-muted">First Name</small>
                  </div>
                  <div class="col-md-4">
                    <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Middle" value="{{ old('middle_name', $assistant->middle_name) }}">
                    <small class="text-muted">Middle Name</small>
                  </div>
                  <div class="col-md-4">
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last" value="{{ old('last_name', $assistant->last_name) }}" required>
                    <small class="text-muted">Last Name</small>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div class="row g-2">
                  <div class="col-md-4">
                    @php $staffOrg = auth()->user()->organization; @endphp
                    @if($staffOrg)
                      <input type="hidden" name="organization_id" value="{{ $staffOrg->id }}">
                      <select class="form-select" disabled>
                        <option>{{ $staffOrg->name }}</option>
                      </select>
                      <small class="text-muted">Organization (auto-set to your organization)</small>
                    @else
                      <select name="organization_id" id="organization_id" class="form-select">
                        <option value="">Select Org</option>
                        @php $orgId = old('organization_id', $assistant->organization_id); @endphp
                        @foreach(\App\Models\Organization::orderBy('name')->get() as $org)
                          <option value="{{ $org->id }}" {{ $orgId == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                        @endforeach
                      </select>
                      <small class="text-muted d-block text-truncate" title="Organization (filtered by Department; includes unassigned)">Organization (by Dept + unassigned)</small>
                    @endif
                  </div>
                  <div class="col-md-4 ms-1">
                    <select name="department_id" id="department_id" class="form-select">
                      <option value="">Select Dept</option>
                      @php $deptId = old('department_id', $assistant->department_id); @endphp
                      @foreach(\App\Models\Department::all() as $dept)
                        <option value="{{ $dept->id }}" {{ $deptId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                      @endforeach
                    </select>
                    <small class="text-muted">Department</small>
                  </div>
                  <div class="col-md-4">
                    @php
                      $positions = [
                        'Org. Coordinator',
                        'Org. President',
                        'Org. Vice President for External Affairs',
                        'Org. Vice President for Internal Affairs',
                        'Org. Associate Secretary',
                        'Org. General Secretayr',
                        'Org. Treasurer',
                        'Org. Auditor',
                        'Org. Public Relations Officers (1)',
                        'Org. Public Relations Officers (2)',
                        'Org. Sgt, at Arms(1)',
                        'Org. Sgt, at Arms(2)',
                        'Org. Year Level Representative (1)',
                        'Org. Year Level Representative (2)',
                        'Org. Year Level Representative (3)',
                        'Org. Year Level Representative (3)',
                        'Org. Ms. Representative',
                        'Org. Mr. Representative',
                        'Others(1)',
                        'Others(2)',
                      ];
                      $posVal = old('position', $assistant->position);
                    @endphp
                    <select name="position" id="position" class="form-select">
                      <option value="">Select Position</option>
                      @foreach($positions as $pos)
                        <option value="{{ $pos }}" {{ $posVal == $pos ? 'selected' : '' }}>{{ $pos }}</option>
                      @endforeach
                    </select>
                    <small class="text-muted">Position (optional)</small>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div class="row g-2 align-items-start">
                  <div class="col-md-4">
                    <input type="file" name="service_order" id="service_order" class="form-control" accept=".pdf,.doc,.docx">
                    <small class="text-muted">S.O. (Service Order)</small>
                  </div>
                  <div class="col-md-4">
                    <input type="number" name="length_of_service" id="length_of_service" class="form-control" min="0" placeholder="Yrs" value="{{ old('length_of_service', $assistant->length_of_service) }}">
                    <small class="text-muted">Length of Service</small>
                  </div>
                  <div class="col-md-4">
                    @php $endVal = old('contract_end_at', optional($assistant->contract_end_at)->format('m/d/Y')); @endphp
                    <input type="text" name="contract_end_at" id="contract_end_at" class="form-control" placeholder="MM/DD/YYYY" pattern="^\d{2}\/\d{2}\/\d{4}$" inputmode="numeric" value="{{ $endVal }}">
                    <small class="text-muted">End of Contract (MM/DD/YYYY)</small>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <div class="row g-2">
                  <div class="col-md-6">
                    <input type="password" name="password" id="password" class="form-control" placeholder="New Password (optional)">
                    <small class="text-muted">New Password (optional)</small>
                  </div>
                  <div class="col-md-6">
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    <small class="text-muted">Profile Image</small>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="mt-3 d-flex justify-content-end">
          <a href="{{ route('staff.assistants.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </main>
  </div>
</div>
@endsection
