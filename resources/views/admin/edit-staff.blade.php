@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.show-staff') }}" class="btn btn-secondary">Back to Staff List</a>
    </div>
    <div class="container-fluid">
        <div class="row">
            @include('admin.partials.sidebar')
            <main class="col-md-10">
                <h3 class="mt-4"><span class="d-block w-100 px-3 py-2" style="background-color: midnightblue; color: white; border-radius: 4px;">Edit Staff</span></h3>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.staff.update', $staff->id) }}" enctype="multipart/form-data" class="card p-4 mb-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="employment_status" id="employment_status" value="{{ $staff->employment_status }}">
                    <table class="table table-borderless">
                        <tbody>
                <tr>
                    <td colspan="2">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="user_id" id="user_id" class="form-control" placeholder="Employee ID" value="{{ old('user_id', $staff->user_id) }}" required>
                                <small class="text-muted">Employee ID</small>
                            </div>
                            <div class="col-md-4">
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="{{ old('email', $staff->email) }}" required>
                                <small class="text-muted">Email</small>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="Contact" value="{{ old('contact_number', $staff->contact_number) }}">
                                <small class="text-muted">Contact Number</small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First" value="{{ old('first_name', $staff->first_name) }}" required>
                                <small class="text-muted">First Name</small>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Middle" value="{{ old('middle_name', $staff->middle_name) }}">
                                <small class="text-muted">Middle Name</small>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last" value="{{ old('last_name', $staff->last_name) }}" required>
                                <small class="text-muted">Last Name</small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row g-2">
                            <div class="col-md-6" data-birth-age-pair>
                                @include('components.birthdate_with_age', ['name' => 'birth_date', 'ageName' => 'age', 'value' => old('birth_date', $staff->birth_date)])
                                <small class="text-muted">Birth Date</small>
                            </div>
                            <div class="col-md-6">
                                <select name="gender" id="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male" @selected(old('gender', $staff->gender) === 'male')>Male</option>
                                    <option value="female" @selected(old('gender', $staff->gender) === 'female')>Female</option>
                                    <option value="other" @selected(old('gender', $staff->gender) === 'other')>Other</option>
                                </select>
                                <small class="text-muted">Gender</small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div>
                                    <label class="form-label">Organizations</label>
                                    <div class="d-flex flex-wrap">
                                        @isset($organizations)
                                            @foreach($organizations as $org)
                                                <div class="form-check me-3 mb-2">
                                                    <input class="form-check-input" type="checkbox" name="organization_ids[]" id="org{{ $org->id }}" value="{{ $org->id }}"
                                                        @if(collect(old('organization_ids', $staff->organizations->pluck('id')->toArray()))->contains($org->id)) checked @endif>
                                                    <label class="form-check-label" for="org{{ $org->id }}">{{ $org->name }}</label>
                                                </div>
                                            @endforeach
                                        @endisset
                                    </div>
                                    <small class="text-muted d-block text-truncate" title="Organization (filtered by Department; includes unassigned)">Select one or more organizations</small>
                                </div>
                            </div>
                            <div class="col-md-4 ms-1">
                                <select name="department_id" id="department_id" class="form-select">
                                    <option value="">Select Dept</option>
                                    @isset($departments)
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" @selected(old('department_id', $staff->department_id) == $dept->id)>{{ $dept->name }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                                <small class="text-muted">Department</small>
                            </div>
                            <div class="col-md-4">
                                <select name="designation" id="designation" class="form-select" required>
                                    <option value="">Select Desig</option>
                                    @php($designations = \App\Models\Designation::all())
                                    @foreach($designations as $designation)
                                        <option value="{{ $designation->name }}" @selected(old('designation', $staff->designation) === $designation->name)>{{ $designation->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Designation</small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row g-2 align-items-start">
                            <div class="col-md-4">
                                @if($staff->service_order)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($staff->service_order) }}" target="_blank" class="btn btn-info btn-sm mb-2">Download S.O.</a>
                                @endif
                                <input type="file" name="service_order" id="service_order" class="form-control" accept=".pdf,.doc,.docx">
                                <small class="text-muted">S.O. (Service Order)</small>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="length_of_service" id="length_of_service" class="form-control" min="0" placeholder="Yrs" value="{{ old('length_of_service', $staff->length_of_service) }}">
                                <small class="text-muted">Length of Service</small>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="contract_end_at" id="contract_end_at" class="form-control" placeholder="MM/DD/YYYY" value="{{ $staff->contract_end_at ? \Carbon\Carbon::parse($staff->contract_end_at)->format('m/d/Y') : '' }}" pattern="^\d{2}\/\d{2}\/\d{4}$" inputmode="numeric">
                                <small class="text-muted">End of Contract (MM/DD/YYYY)</small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password (optional)">
                                <small class="text-muted d-block mb-2">Leave blank to keep current password.</small>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="Confirm New Password (optional)">
                            </div>
                            <div class="col-md-6">
                                @if($staff->image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($staff->image) }}" alt="Profile Image" class="img-thumbnail mb-2" width="120">
                                @endif
                                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                                <small class="text-muted">Profile Image</small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-danger" type="button" id="btnEndContract">Terminate</button>
                            <button class="btn btn-outline-secondary" type="button" id="btnSuspend">Suspend</button>
                            <button class="btn btn-outline-success" type="button" id="btnActivate">Restart</button>
                        </div>
                    </td>
                </tr>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success">Update Staff</button>
                </form>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const deptSel = document.getElementById('department_id');
  const orgSel = document.getElementById('organization_id');
  const selectedOrg = '{{ $staff->organization_id }}';

  async function loadOrganizations(deptId){
    try {
      const params = deptId ? `?department_id=${encodeURIComponent(deptId)}` : `?unassigned=1`;
      const res = await fetch(`/api/organizations${params}`);
      const data = await res.json();
      let items = data;
      if (deptId) {
        const unassignedRes = await fetch(`/api/organizations?unassigned=1`);
        const unassigned = await unassignedRes.json();
        const byId = new Map();
        [...items, ...unassigned].forEach(it => byId.set(it.id, it));
        items = Array.from(byId.values());
      }
      orgSel.innerHTML = '';
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Select Org';
      orgSel.appendChild(placeholder);
      items.sort((a,b)=> a.name.localeCompare(b.name)).forEach(org => {
        const opt = document.createElement('option');
        opt.value = org.id;
        opt.textContent = org.name;
        orgSel.appendChild(opt);
      });
      if (selectedOrg) {
        orgSel.value = selectedOrg;
      }
    } catch (e) {
      // ignore; keep existing options
    }
  }

  if (deptSel) {
    deptSel.addEventListener('change', ()=>{
      const id = deptSel.value || '';
      loadOrganizations(id);
    });
    if (!deptSel.value) {
      loadOrganizations('');
    } else {
      loadOrganizations(deptSel.value);
    }
  }

  // Action buttons wire-up using hidden employment_status and MM/DD/YYYY date
  const statusInput = document.getElementById('employment_status');
  const endBtn = document.getElementById('btnEndContract');
  const suspendBtn = document.getElementById('btnSuspend');
  const restartBtn = document.getElementById('btnActivate');
  const endInput = document.getElementById('contract_end_at');

  function todayMMDDYYYY(){
    const d = new Date();
    const mm = String(d.getMonth()+1).padStart(2,'0');
    const dd = String(d.getDate()).padStart(2,'0');
    const yyyy = d.getFullYear();
    return `${mm}/${dd}/${yyyy}`;
  }

  endBtn?.addEventListener('click', ()=>{
    if (statusInput) statusInput.value = 'ended';
    if (endInput) endInput.value = todayMMDDYYYY();
  });
  suspendBtn?.addEventListener('click', ()=>{
    if (statusInput) statusInput.value = 'inactive';
  });
  restartBtn?.addEventListener('click', ()=>{
    if (statusInput) statusInput.value = 'active';
  });
});
</script>
@endpush
