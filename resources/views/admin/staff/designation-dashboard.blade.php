@extends('layouts.app')

@section('title', $designation->name . ' Dashboard')

@section('content')
<div class="container-fluid">
  <div class="row">
    @if (strcasecmp($designation->name, 'Admission Services Officer') !== 0)
    <div class="col-md-3 col-lg-2">
      <div class="list-group mb-3">
        <div class="list-group-item active" style="background-color: midnightblue; border-color: midnightblue;">Quick Actions</div>
        <a href="{{ route('admin.appointments.index', ['return_to' => urlencode(route('admin.staff.dashboard.designation', ['designation' => $designation->name]))]) }}" class="list-group-item list-group-item-action">Assigned Appointments</a>
        @if(isset($isStaff) && $isStaff)
          <a href="{{ route('staff.organizations.index') }}" class="list-group-item list-group-item-action">My Organization</a>
        @endif
        <a href="{{ route('admin.events.index', ['return_to' => urlencode(route('admin.staff.dashboard.designation', ['designation' => $designation->name]))]) }}" class="list-group-item list-group-item-action">All Events</a>
        @if(isset($isAdmin) && $isAdmin)
          <a href="{{ route('admin.events.index', ['return_to' => urlencode(route('admin.staff.dashboard.designation', ['designation' => $designation->name]))]) }}#create" class="list-group-item list-group-item-action">Create Event</a>
        @endif
        <a href="{{ route('admin.participants.export', ['return_to' => urlencode(route('admin.staff.dashboard.designation', ['designation' => $designation->name]))]) }}" class="list-group-item list-group-item-action">Participants History</a>
        <a href="{{ route('admin.staff.dashboard.report', ['designation' => $designation->name]) }}" class="list-group-item list-group-item-action">Reports</a>
        <a href="{{ route('admin.organizational-structure') }}" class="list-group-item list-group-item-action">
          <i class="bi bi-diagram-3"></i> Organizational Structure
        </a>
      </div>
    </div>
    @endif

    <main class="{{ strcasecmp($designation->name, 'Admission Services Officer') === 0 ? 'col-12' : 'col-md-9 col-lg-10' }}">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><span class="px-2 py-1" style="background-color: midnightblue; color: white; border-radius: 4px;">{{ $designation->name }} — Staff</span></h2>
        @if (strcasecmp($designation->name, 'Admission Services Officer') === 0)
          <div class="d-flex gap-2">
            <a href="{{ route('admin.staff.dashboard') }}" class="btn btn-secondary">All Staff Dashboards</a>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Staff Dashboard</a>
          </div>
        @endif
      </div>

      @if (strcasecmp($designation->name, 'Admission Services Officer') === 0)
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>All Students</h5>
            <a href="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student-management') }}" class="btn btn-primary">Add Student</a>
          </div>
          
          <!-- Search and Filter Section -->
          <form method="GET" action="{{ request()->url() }}" class="mb-3">
            <div class="row">
              <div class="col-md-4 mb-2">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search by name, email, ID, or contact..." 
                       value="{{ request('search') }}">
              </div>
              <div class="col-md-3 mb-2">
                <select name="department_id" class="form-control">
                  <option value="">All Departments</option>
                  @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                      {{ $dept->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 mb-2">
                <select name="year_level" class="form-control">
                  <option value="">All Year Levels</option>
                  <option value="1" {{ request('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                  <option value="2" {{ request('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                  <option value="3" {{ request('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                  <option value="4" {{ request('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                  <option value="5" {{ request('year_level') == '5' ? 'selected' : '' }}>5th Year</option>
                </select>
              </div>
              <div class="col-md-3 mb-2">
                <button type="submit" class="btn btn-primary w-100">Search & Filter</button>
              </div>
            </div>
            @if(request()->has('search') || request()->has('department_id') || request()->has('year_level'))
              <div class="mt-2">
                <a href="{{ request()->url() }}" class="btn btn-sm btn-secondary">Clear Filters</a>
                <small class="text-muted ml-2">
                  Showing {{ $students->count() }} result(s)
                </small>
              </div>
            @endif
          </form>
          
          <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
              <thead style="background-color:midnightblue; color:white">
                <tr>
                  <th>Student ID</th>
                  <th>First Name</th>
                  <th>Middle Name</th>
                  <th>Last Name</th>
                  <th>Contact Number</th>
                  <th>Email</th>
                  <th>Department</th>
                  <th>Course</th>
                  <th>Organization</th>
                  <th>Year Level</th>
                  <th>Gender</th>
                  <th>Birth Date</th>
                  <th>Type 1</th>
                  <th>Type 2</th>
                  <th>Scholarship</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @if(!request()->has('search') && !request()->has('department_id') && !request()->has('year_level'))
                  <tr>
                    <td colspan="17" class="text-center py-4">
                      <p class="text-muted mb-0">
                        Please enter a search term or select a department/year level filter to find students.
                      </p>
                    </td>
                  </tr>
                @elseif($students->isEmpty())
                  <tr>
                    <td colspan="17" class="text-center py-4">
                      <p class="text-muted mb-0">
                        No students found matching your search criteria.
                      </p>
                    </td>
                  </tr>
                @else
                  @foreach($students as $student)
                  <tr>
                    <td>{{ $student->user_id_display ?? $student->user->user_id ?? $student->user_id ?? $student->id }}</td>
                    <td>{{ $student->first_name ?? $student->user->first_name ?? '-' }}</td>
                    <td>{{ $student->middle_name ?? $student->user->middle_name ?? '' }}</td>
                    <td>{{ $student->last_name ?? $student->user->last_name ?? '-' }}</td>
                    <td>{{ $student->contact_number ?? $student->user->contact_number ?? '' }}</td>
                    <td>{{ $student->email ?? $student->user->email ?? '-' }}</td>
                    <td>{{ optional($student->department)->name ?? (optional($student->user->department)->name ?? '-') }}</td>
                    <td>{{ optional($student->course)->name ?? (optional($student->user->course)->name ?? '-') }}</td>
                    <td>{{ optional($student->organization)->name ?? (optional($student->user->organization)->name ?? '-') }}</td>
                    <td>{{ $student->year_level ?? $student->user->year_level ?? '-' }}</td>
                    <td>{{ ucfirst($student->gender ?? $student->user->gender ?? '-') }}</td>
                    <td>{{ $student->birth_date ?? $student->user->birth_date ?? '-' }}</td>
                    <td>{{ ucfirst($student->student_type1 ?? $student->user->student_type1 ?? '-') }}</td>
                    <td>{{ ucfirst($student->student_type2 ?? $student->user->student_type2 ?? '-') }}</td>
                    <td>{{ optional($student->scholarship)->name ?? (optional($student->user->scholarship)->name ?? '-') }}</td>
                    <td>{{ $student->status ?? $student->user->status ?? '-' }}</td>
                    <td>
                      @php
                        // Determine if this is a Student model or User model
                        $isStudentModel = isset($student->isStudentModel) && $student->isStudentModel;
                        $isUserModel = isset($student->isUserModel) && $student->isUserModel;
                        // For Student model, use its id; for User model, check if it has a student record
                        $studentId = $isStudentModel ? $student->id : ($isUserModel && $student->student ? $student->student->id : null);
                      @endphp
                      @if($studentId)
                        <a href="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student.edit', $studentId) }}" class="btn btn-sm btn-warning">Update</a>
                        <form action="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student.destroy', $studentId) }}" method="POST" style="display:inline-block;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">Delete</button>
                        </form>
                      @elseif($isUserModel)
                        <span class="badge badge-info">User Only</span>
                        <small class="text-muted d-block">No Student Record</small>
                      @else
                        <span class="badge badge-secondary">No ID</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Add Student Modal -->
      <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST" action="{{ route('admin.staff.dashboard.AdmissionServicesOfficer.student-management.store') }}">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-2">
                  <label for="student_id" class="form-label">Student ID</label>
                  <input type="text" class="form-control" name="student_id" required>
                </div>
                <div class="mb-2">
                  <label for="first_name" class="form-label">First Name</label>
                  <input type="text" class="form-control" name="first_name" required>
                </div>
                <div class="mb-2">
                  <label for="middle_name" class="form-label">Middle Name</label>
                  <input type="text" class="form-control" name="middle_name">
                </div>
                <div class="mb-2">
                  <label for="last_name" class="form-label">Last Name</label>
                  <input type="text" class="form-control" name="last_name" required>
                </div>
                <div class="mb-2">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-2">
                  <label for="contact_number" class="form-label">Contact Number</label>
                  <input type="text" class="form-control" name="contact_number">
                </div>
                <div class="mb-2">
                  <label for="department" class="form-label">Department</label>
                  <input type="text" class="form-control" name="department" required>
                </div>
                <div class="mb-2">
                  <label for="course" class="form-label">Course</label>
                  <input type="text" class="form-control" name="course" required>
                </div>
                <div class="mb-2">
                  <label for="year_level" class="form-label">Year Level</label>
                  <input type="text" class="form-control" name="year_level">
                </div>
                <div class="mb-2">
                  <label for="gender" class="form-label">Gender</label>
                  <input type="text" class="form-control" name="gender">
                </div>
                <div class="mb-2">
                  <label for="birth_date" class="form-label">Birth Date</label>
                  <input type="date" class="form-control" name="birth_date">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Student</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      @endif

      @if (strcasecmp($designation->name, 'Guidance Counsellor') === 0)
      <div class="card mb-4">
        <div class="card-header" style="background-color: midnightblue; color: white;">
          <h5 class="mb-0"><i class="bi bi-list-ul"></i> Guidance Services</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <a href="#" class="btn btn-outline-primary btn-lg w-100 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                <div class="text-center">
                  <i class="bi bi-person-check fs-3 d-block mb-2"></i>
                  <strong>Initial Interview</strong>
                </div>
              </a>
            </div>
            <div class="col-md-6 mb-3">
              <a href="#" class="btn btn-outline-info btn-lg w-100 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                <div class="text-center">
                  <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                  <strong>Information Services</strong>
                </div>
              </a>
            </div>
            <div class="col-md-6 mb-3">
              <a href="#" class="btn btn-outline-success btn-lg w-100 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                <div class="text-center">
                  <i class="bi bi-chat-dots fs-3 d-block mb-2"></i>
                  <strong>Counseling Services</strong>
                </div>
              </a>
            </div>
            <div class="col-md-6 mb-3">
              <a href="#" class="btn btn-outline-warning btn-lg w-100 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                <div class="text-center">
                  <i class="bi bi-arrow-up-right-circle fs-3 d-block mb-2"></i>
                  <strong>External Referral</strong>
                </div>
              </a>
            </div>
            <div class="col-md-6 mb-3">
              <a href="#" class="btn btn-outline-secondary btn-lg w-100 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                <div class="text-center">
                  <i class="bi bi-arrow-right-circle fs-3 d-block mb-2"></i>
                  <strong>Internal Referral</strong>
                </div>
              </a>
            </div>
            <div class="col-md-6 mb-3">
              <a href="#" class="btn btn-outline-danger btn-lg w-100 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                <div class="text-center">
                  <i class="bi bi-person-x fs-3 d-block mb-2"></i>
                  <strong>Exit Interview</strong>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
      @endif

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr align="center" style="background-color: midnightblue; color: white;">
              </thead>
              <tbody>
                <!-- Table content goes here -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
  const worksheetData = @json(session('importedWorksheetData') ?? []);
  function filterWorksheet() {
    const form = document.getElementById('searchForm');
    const value = form.search_value.value.trim().toLowerCase();
    const filter = form.search_filter.value;
    let filtered = worksheetData.filter(row => {
      if (!value) return true;
      if (filter === 'Level') {
        // Accept both '1st yr', '2nd yr', etc. and numeric values
        const level = (row['Level']||'').toLowerCase();
        return level.includes(value) || level.replace(/[^0-9]/g, '') === value.replace(/[^0-9]/g, '');
      }
      return ((row[filter]||'').toLowerCase().includes(value));
    });
    let html = '';
    if (filtered.length === 0) {
      html = '<div class="alert alert-warning">No matching data found.</div>';
    } else {
      html = `<table class=\"table table-bordered\"><thead><tr>
        <th>Student No</th>
        <th>Full Name</th>
        <th>Program</th>
        <th>Gender</th>
        <th>Level</th>
        <th>Validation Date</th>
        <th>Email</th>
        <th>Contact</th>
      </tr></thead><tbody>`;
      filtered.forEach((row, idx) => {
        html += `<tr>
          <td contenteditable='true' oninput=\"updateCell(${idx}, 'Student No', this.innerText)\">${row['Student No']||''}</td>
          <td contenteditable='true' oninput=\"updateCell(${idx}, 'Full Name', this.innerText)\">${row['Full Name']||''}</td>
          <td contenteditable='true' oninput=\"updateCell(${idx}, 'Program', this.innerText)\">${row['Program']||''}</td>
          <td contenteditable='true' oninput=\"updateCell(${idx}, 'Gender', this.innerText)\">${row['Gender']||''}</td>
          <td contenteditable='true' oninput=\"updateCell(${idx}, 'Level', this.innerText)\">${row['Level']||''}</td>
          <td contenteditable='true' oninput=\"updateCell(${idx}, 'Validation Date', this.innerText)\">${row['Validation Date']||''}</td>
          <td contenteditable='true' oninput=\"updateCell(${idx}, 'Email', this.innerText)\">${row['Email']||''}</td>
          <td contenteditable='true' oninput=\"updateCell(${idx}, 'Contact', this.innerText)\">${row['Contact']||''}</td>
        </tr>`;
      });
      html += `</tbody></table><div class='mt-2 text-end'><strong>Total matches found: ${filtered.length}</strong></div>`;
      if (filtered.length > 0) {
        html += `<div class='mt-3 text-end'><button type='button' class='btn btn-primary' onclick='updateWorksheet()'>Update</button></div>`;
        html += `<div id='updateSuccessMsg' class='mt-2'></div>`;
      }
  // Update worksheet: catch all changes and save to backend
  function updateWorksheet() {
    // Collect current table data
    const table = document.querySelector('#searchResults table');
    if (!table) return;
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText.trim());
    const updatedRows = Array.from(table.querySelectorAll('tbody tr')).map(tr => {
      const cells = Array.from(tr.querySelectorAll('td'));
      const rowObj = {};
      cells.forEach((td, idx) => {
        rowObj[headers[idx]] = td.innerText.trim();
      });
      return rowObj;
    });
    fetch('/admin/staff/dashboard/save-updated-worksheet', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ rows: updatedRows })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById('updateSuccessMsg').innerHTML = `<div class='alert alert-success'>All changes saved! File: ${data.filename}</div>`;
        worksheetData.length = 0;
        updatedRows.forEach(r => worksheetData.push(r));
        filterWorksheet();
      } else {
        document.getElementById('updateSuccessMsg').innerHTML = `<div class='alert alert-danger'>Failed to save changes.</div>`;
      }
    })
    .catch(() => alert('Failed to save changes.'));
  }
  // Update cell value in worksheetData
  function updateCell(idx, key, value) {
    worksheetData[idx][key] = value;
  }
    }
    document.getElementById('searchResults').innerHTML = html;
  }

  function applyChanges(idx) {
    const form = document.getElementById('editForm');
    worksheetData[idx]['Student No'] = form[`student_no_${idx}`].value;
    worksheetData[idx]['Full Name'] = form[`full_name_${idx}`].value;
    worksheetData[idx]['Program'] = form[`program_${idx}`].value;
    worksheetData[idx]['Courses'] = form[`courses_${idx}`].value;
    alert('Changes applied to this entry. (Note: This only updates the view, not the file. Backend update required for persistence.)');
  }

@endsection