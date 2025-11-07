@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.partials.sidebar')
            <main class="col-md-10">
                <div class="admin-back-btn-wrap">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Dashboard</a>
                </div>
                <style>
                    .section-header { display:block; width:100%; box-sizing:border-box; background:#fff; color: midnightblue; padding:.5rem 1rem; border:none; border-bottom:1px solid midnightblue; border-radius:0; }
                </style>
                <h3 class="mt-4"><span class="section-header">Add Staff</span></h3>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.staff.store') }}" enctype="multipart/form-data" class="card p-4 mb-4">
                    @csrf
                    <table class="table table-borderless">
                        <tbody>
                <tr>
                    <td colspan="2">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="user_id" id="user_id" class="form-control" placeholder="Employee ID" required>
                                <small class="text-muted">Employee ID</small>
                            </div>
                            <div class="col-md-4">
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                <small class="text-muted">Email</small>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="Contact">
                                <small class="text-muted">Contact Number</small>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First" required>
                                <small class="text-muted">First Name</small>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Middle">
                                <small class="text-muted">Middle Name</small>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last" required>
                                <small class="text-muted">Last Name</small>
                            </div>
                        </div>
                    </td>
                </tr>
                
                    

                    <tr>
                        <td colspan="2">
                            <div class="row g-2">
                                <div class="col-md-6" data-birth-age-pair>
                                    @include('components.birthdate_with_age', ['name' => 'birth_date', 'ageName' => 'age', 'value' => old('birth_date')])
                                    <small class="text-muted">Birth Date</small>
                                </div>
                                <div class="col-md-6">
                                    <select name="gender" id="gender" class="form-select">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
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
                                        <label class="form-label">Department-Related Organization</label>
                                        <div id="department-org-container">
                                            <p class="text-muted small mb-2">Select a department first to see department-related organization</p>
                                        </div>
                                        <small class="text-muted d-block">Can select only one (if department is selected)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <label class="form-label">Non-Academic Organizations</label>
                                        <div id="non-academic-org-container" class="d-flex flex-wrap">
                                            <p class="text-muted small mb-2">Loading organizations...</p>
                                        </div>
                                        <small class="text-muted d-block">Can select multiple non-academic organizations</small>
                                    </div>
                                </div>
                                <div class="col-md-4 ms-1">
                                    <select name="department_id" id="department_id" class="form-select">
                                        <option value="">Select Dept</option>
                                        @isset($departments)
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    <small class="text-muted">Department</small>
                                </div>
                                <div class="col-md-4">
                                    <select name="designation" id="designation" class="form-select" required>
                                        <option value="">Select Desig</option>
                                        @php
                                            $designations = \App\Models\Designation::all();
                                        @endphp
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->name }}">{{ $designation->name }}</option>
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
                                    <input type="file" name="service_order" id="service_order" class="form-control" accept=".pdf,.doc,.docx">
                                    <small class="text-muted">S.O. (Service Order)</small>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="length_of_service" id="length_of_service" class="form-control" min="0" placeholder="Yrs">
                                    <small class="text-muted">Length of Service</small>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="contract_end_at" id="contract_end_at" class="form-control" placeholder="MM/DD/YYYY" pattern="^\d{2}\/\d{2}\/\d{4}$" inputmode="numeric">
                                    <small class="text-muted">End of Contract (MM/DD/YYYY)</small>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                    <small class="text-muted">Password</small>
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
                    <button type="submit" class="btn btn-success">Add Staff</button>
                </form>
            </main>
        </div>
    </div>
@endsection
                @push('scripts')
                <script>
                document.addEventListener('DOMContentLoaded', function(){
                    const deptSel = document.getElementById('department_id');
                    const deptOrgContainer = document.getElementById('department-org-container');
                    const nonAcademicContainer = document.getElementById('non-academic-org-container');
                    let selectedDeptOrgId = null;
                    let selectedNonAcademicIds = new Set();

                    // Save currently selected organizations before reloading
                    function saveSelectedOrganizations() {
                        // Save department org (radio button)
                        const deptRadio = deptOrgContainer.querySelector('input[type="radio"]:checked');
                        selectedDeptOrgId = deptRadio ? deptRadio.value : null;
                        
                        // Save non-academic orgs (checkboxes)
                        selectedNonAcademicIds.clear();
                        const checkboxes = nonAcademicContainer.querySelectorAll('input[type="checkbox"]:checked');
                        checkboxes.forEach(cb => selectedNonAcademicIds.add(cb.value));
                    }

                    // Restore selected organizations after reloading
                    function restoreSelectedOrganizations() {
                        // Restore department org
                        if (selectedDeptOrgId) {
                            const radio = deptOrgContainer.querySelector(`input[type="radio"][value="${selectedDeptOrgId}"]`);
                            if (radio) {
                                radio.checked = true;
                            }
                        }
                        
                        // Restore non-academic orgs
                        selectedNonAcademicIds.forEach(orgId => {
                            const checkbox = nonAcademicContainer.querySelector(`input[type="checkbox"][value="${orgId}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                    }

                    async function loadNonAcademicOrganizations(){
                        try {
                            const res = await fetch(`/api/organizations?unassigned=1`);
                            const data = await res.json();
                            
                            // Clear container
                            nonAcademicContainer.innerHTML = '';
                            
                            // Sort organizations by name
                            data.sort((a, b) => a.name.localeCompare(b.name));
                            
                            // Create checkboxes for each non-academic organization
                            data.forEach(org => {
                                const div = document.createElement('div');
                                div.className = 'form-check me-3 mb-2';
                                
                                const checkbox = document.createElement('input');
                                checkbox.className = 'form-check-input non-academic-org';
                                checkbox.type = 'checkbox';
                                checkbox.name = 'organization_ids[]';
                                checkbox.id = `org${org.id}`;
                                checkbox.value = org.id;
                                
                                const label = document.createElement('label');
                                label.className = 'form-check-label';
                                label.htmlFor = `org${org.id}`;
                                label.textContent = org.name;
                                
                                div.appendChild(checkbox);
                                div.appendChild(label);
                                nonAcademicContainer.appendChild(div);
                            });
                        } catch (e) {
                            console.error('Error loading non-academic organizations:', e);
                        }
                    }

                    async function loadDepartmentOrganization(deptId){
                        // Save current selections
                        saveSelectedOrganizations();
                        
                        try {
                            if (deptId) {
                                // Fetch department-related organizations for this department
                                const res = await fetch(`/api/organizations?department_id=${encodeURIComponent(deptId)}`);
                                const data = await res.json();
                                
                                // Filter to only department-related orgs (those with department_id matching the selected dept)
                                const deptOrgs = data.filter(org => org.department_id != null && parseInt(org.department_id) === parseInt(deptId));
                                
                                // Clear container
                                deptOrgContainer.innerHTML = '';
                                
                                if (deptOrgs.length > 0) {
                                    // Create radio buttons for department-related organizations
                                    // Only one can be selected
                                    deptOrgs.forEach(org => {
                                        const div = document.createElement('div');
                                        div.className = 'form-check mb-2';
                                        
                                        const radio = document.createElement('input');
                                        radio.className = 'form-check-input department-org';
                                        radio.type = 'radio';
                                        radio.name = 'department_organization_id';
                                        radio.id = `dept-org${org.id}`;
                                        radio.value = org.id;
                                        
                                        // Handle radio button change - add to organization_ids
                                        radio.addEventListener('change', function() {
                                            if (this.checked) {
                                                // Uncheck other department org radios
                                                deptOrgContainer.querySelectorAll('input[type="radio"]').forEach(r => {
                                                    if (r !== this) r.checked = false;
                                                });
                                                
                                                // Remove any existing department org hidden inputs
                                                deptOrgContainer.querySelectorAll('.dept-org-hidden').forEach(el => el.remove());
                                                
                                                // Add this org to organization_ids
                                                const hidden = document.createElement('input');
                                                hidden.type = 'hidden';
                                                hidden.name = 'organization_ids[]';
                                                hidden.value = org.id;
                                                hidden.className = 'dept-org-hidden';
                                                deptOrgContainer.appendChild(hidden);
                                            } else {
                                                // Remove from organization_ids
                                                const hiddenInput = deptOrgContainer.querySelector(`input[name="organization_ids[]"][value="${org.id}"].dept-org-hidden`);
                                                if (hiddenInput) {
                                                    hiddenInput.remove();
                                                }
                                            }
                                        });
                                        
                                        const label = document.createElement('label');
                                        label.className = 'form-check-label';
                                        label.htmlFor = `dept-org${org.id}`;
                                        label.textContent = org.name;
                                        
                                        div.appendChild(radio);
                                        div.appendChild(label);
                                        deptOrgContainer.appendChild(div);
                                    });
                                } else {
                                    deptOrgContainer.innerHTML = '<p class="text-muted small mb-2">No department-related organization found for this department</p>';
                                    // Remove any hidden department org inputs
                                    deptOrgContainer.querySelectorAll('.dept-org-hidden').forEach(el => el.remove());
                                }
                            } else {
                                // No department selected - clear department org container
                                deptOrgContainer.innerHTML = '<p class="text-muted small mb-2">Select a department first to see department-related organization</p>';
                                // Remove any hidden department org inputs
                                deptOrgContainer.querySelectorAll('.dept-org-hidden').forEach(el => el.remove());
                            }
                            
                            // Restore selected organizations
                            restoreSelectedOrganizations();
                            
                            // If department org was restored, trigger change to add to organization_ids
                            if (selectedDeptOrgId) {
                                const restoredRadio = deptOrgContainer.querySelector(`input[type="radio"][value="${selectedDeptOrgId}"]`);
                                if (restoredRadio) {
                                    restoredRadio.dispatchEvent(new Event('change'));
                                }
                            }
                        } catch (e) {
                            console.error('Error loading department organization:', e);
                            deptOrgContainer.innerHTML = '<p class="text-muted small mb-2">Error loading department organization</p>';
                        }
                    }

                    if (deptSel) {
                        deptSel.addEventListener('change', ()=>{
                            const id = deptSel.value || '';
                            loadDepartmentOrganization(id);
                        });

                        // Load non-academic organizations on page load
                        loadNonAcademicOrganizations();
                        
                        // Load department organization if department is already selected
                        if (deptSel.value) {
                            loadDepartmentOrganization(deptSel.value);
                        }
                    }
                });
                </script>
                @endpush