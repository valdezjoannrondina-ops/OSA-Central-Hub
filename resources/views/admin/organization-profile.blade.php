@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row">
    @include('admin.partials.sidebar')
    <main class="col-md-10 py-4">
        <div class="admin-back-btn-wrap mb-3">
            <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Organizations</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0">{{ $organization->name }}</h1>
                <a href="{{ route('admin.organizational-structure', ['organization_id' => $organization->id]) }}" class="btn btn-primary">
                    <i class="bi bi-diagram-3"></i> Organizational Structure
                </a>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Organization Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Name:</th>
                                    <td>{{ $organization->name }}</td>
                                </tr>
                                @if($organization->acronym)
                                <tr>
                                    <th>Acronym:</th>
                                    <td>{{ $organization->acronym }}</td>
                                </tr>
                                @endif
                                @if($organization->department)
                                <tr>
                                    <th>Department:</th>
                                    <td>{{ $organization->department->name }}</td>
                                </tr>
                                @endif
                                @if(!$organization->department)
                                <tr>
                                    <th>Type:</th>
                                    <td><span class="badge bg-info">Non-Academic Organization</span></td>
                                </tr>
                                @endif
                                @if($organization->official_email)
                                <tr>
                                    <th>Official Email:</th>
                                    <td>{{ $organization->official_email }}</td>
                                </tr>
                                @endif
                                @if($organization->mailing_address)
                                <tr>
                                    <th>Mailing Address:</th>
                                    <td>{{ $organization->mailing_address }}</td>
                                </tr>
                                @endif
                                @if($organization->date_established)
                                <tr>
                                    <th>Date Established:</th>
                                    <td>{{ \Carbon\Carbon::parse($organization->date_established)->format('F j, Y') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Student Membership</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="display-1 text-primary">{{ $studentCount }}</div>
                            <p class="lead mb-0">Total Students</p>
                            <small class="text-muted">Members of this organization</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($allStudents->count() > 0)
            <div class="card">
                <div class="card-header bg-blue text-white">
                    <h5 class="mb-0">Student Members List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Course</th>
                                    <th>Year Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allStudents as $student)
                                <tr>
                                    <td>{{ $student->user_id ?? '-' }}</td>
                                    <td>{{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->last_name }}</td>
                                    <td>{{ $student->department->name ?? '-' }}</td>
                                    <td>{{ $student->course->name ?? '-' }}</td>
                                    <td>{{ $student->year_level ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                This organization currently has no student members.
            </div>
            @endif

            <!-- Organization Files Section -->
            <div class="card mt-4">
                <div class="card-header" style="background-color: midnightblue; color: white;">
                    <h5 class="mb-0"><i class="bi bi-folder"></i> Organization Files</h5>
                </div>
                <div class="card-body">
                    @if(session('file_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('file_success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('file_error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('file_error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead style="background-color: #f0f0f0;">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 35%;">File Name</th>
                                    <th style="width: 15%;">File Size</th>
                                    <th style="width: 15%;">Uploaded By</th>
                                    <th style="width: 15%;">Uploaded At</th>
                                    <th style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $fileIndex = 0;
                                    $categoryOrder = [
                                        'accreditation_checklist' => 0,
                                        'application_letter' => 1,
                                        'accreditation_form' => 2,
                                        'concept_paper' => 3,
                                        'constitution' => 4,
                                        'organizational_profile' => 5.1,
                                        'officers_members_list' => 5.2,
                                        'personal_data_sheet' => 5.3,
                                        'organizational structure' => 5.4,
                                        'moderatorship_letter' => 7,
                                    ];
                                    
                                    // Check user access once
                                    $user = auth()->user();
                                    $isAdmin = (int)($user->role ?? 0) === 4;
                                    $isStaff = (int)($user->role ?? 0) === 2;
                                    $hasAccess = false;
                                    if ($isAdmin) {
                                        $hasAccess = true;
                                    } elseif ($isStaff) {
                                        $staff = \App\Models\Staff::where('email', $user->email)->first();
                                        if ($staff) {
                                            if ($staff->organization_id == $organization->id || 
                                                $staff->organizations()->where('organizations.id', $organization->id)->exists()) {
                                                $hasAccess = true;
                                            }
                                        }
                                        if (!$hasAccess && ($user->organization_id == $organization->id || 
                                            (method_exists($user, 'otherOrganizations') && $user->otherOrganizations()->where('organizations.id', $organization->id)->exists()))) {
                                            $hasAccess = true;
                                        }
                                    }
                                @endphp
                                @foreach($requiredFileCategories as $categoryKey => $categoryName)
                                    @php
                                        $categoryFiles = $files->get($categoryKey, collect());
                                        $displayName = $categoryName;
                                        if ($categoryKey === 'constitution') {
                                            $displayName = str_replace('(Org.Name)', $organization->name, $categoryName);
                                        }
                                    @endphp
                                    <tr style="background-color: #f8f9fa;">
                                        <td colspan="6" style="font-weight: bold; padding: 10px;">
                                            <i class="bi bi-file-earmark"></i> {{ $fileIndex }}. {{ $displayName }}
                                        </td>
                                    </tr>
                                    @if($categoryFiles->isEmpty())
                                        <tr>
                                            <td></td>
                                            <td colspan="5" class="text-muted">
                                                <em>No file uploaded</em>
                                                @if($hasAccess)
                                                    <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal" data-target="#uploadModal{{ $fileIndex }}">
                                                        <i class="bi bi-cloud-upload"></i> Upload
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($categoryFiles as $file)
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <i class="bi bi-file-earmark"></i> {{ $file->file_name }}
                                                </td>
                                                <td>{{ $file->human_readable_size }}</td>
                                                <td>{{ $file->uploader->first_name ?? '' }} {{ $file->uploader->last_name ?? '' }}</td>
                                                <td>{{ $file->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.organizations.profile.file.view', [$organization->id, $file->id]) }}" class="btn btn-sm btn-info" target="_blank" title="View Only">
                                                            <i class="bi bi-eye"></i> View Only
                                                        </a>
                                                        <a href="{{ route('admin.organizations.profile.file.download', [$organization->id, $file->id]) }}" class="btn btn-sm btn-primary" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        @if($hasAccess)
                                                            <form action="{{ route('admin.organizations.profile.file.delete', [$organization->id, $file->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($hasAccess)
                                            <tr>
                                                <td></td>
                                                <td colspan="5">
                                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#uploadModal{{ $fileIndex }}">
                                                        <i class="bi bi-cloud-upload"></i> Upload Another File
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                    @php $fileIndex++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- All Upload Modals (placed outside table for proper display) -->
                    @php
                        $fileIndex = 0;
                    @endphp
                    @foreach($requiredFileCategories as $categoryKey => $categoryName)
                        @php
                            $displayName = $categoryName;
                            if ($categoryKey === 'constitution') {
                                $displayName = str_replace('(Org.Name)', $organization->name, $categoryName);
                            }
                        @endphp
                        @if($hasAccess)
                            <div class="modal fade" id="uploadModal{{ $fileIndex }}" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel{{ $fileIndex }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px; margin: 1.75rem auto; z-index: 1051;">
                                    <div class="modal-content" style="background-color: #fff !important; z-index: 1052 !important;">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="uploadModalLabel{{ $fileIndex }}">Upload: {{ $displayName }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('admin.organizations.profile.file.upload', $organization->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="file_category" value="{{ $categoryKey }}">
                                                <div class="form-group">
                                                    <label for="file{{ $fileIndex }}">Select File:</label>
                                                    <input type="file" class="form-control-file" id="file{{ $fileIndex }}" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls,.csv,.txt">
                                                    <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG, XLSX, XLS, CSV, TXT (Max: 20MB)</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description{{ $fileIndex }}">Description (optional):</label>
                                                    <textarea class="form-control" id="description{{ $fileIndex }}" name="description" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Upload File</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @php $fileIndex++; @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </main>
  </div>
</div>

<style>
/* Fix modal-dialog-centered height issue */
.modal-dialog-centered {
    min-height: auto !important;
    height: auto !important;
}

.modal-dialog-centered::before {
    display: none !important;
    height: 0 !important;
    content: none !important;
}

/* Ensure modal dialog is visible and properly sized */
#uploadModal0 .modal-dialog,
#uploadModal1 .modal-dialog,
#uploadModal2 .modal-dialog,
#uploadModal3 .modal-dialog,
#uploadModal4 .modal-dialog,
#uploadModal5 .modal-dialog,
#uploadModal6 .modal-dialog,
#uploadModal7 .modal-dialog,
#uploadModal8 .modal-dialog,
#uploadModal9 .modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
    height: auto !important;
    min-height: auto !important;
    z-index: 1051 !important;
    position: relative !important;
}

/* Ensure modal content is visible */
.modal.show .modal-dialog {
    z-index: 1051 !important;
    position: relative !important;
}

.modal.show .modal-content {
    z-index: 1052 !important;
    position: relative !important;
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Ensure backdrop doesn't cover modal */
.modal-backdrop {
    z-index: 1040 !important;
}

/* Ensure modal is above backdrop */
.modal.show {
    z-index: 1050 !important;
}

.modal.show .modal-dialog {
    z-index: 1051 !important;
}

/* Force modal to be visible and clickable */
.modal.show {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}

.modal.show .modal-dialog {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}

.modal.show .modal-content {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    background-color: #fff !important;
}

/* Ensure backdrop is behind modal */
.modal-backdrop {
    z-index: 1040 !important;
    pointer-events: auto !important;
    opacity: 0.5 !important;
}

/* Ensure modal is above backdrop and visible */
.modal.show {
    z-index: 1050 !important;
    pointer-events: auto !important;
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Ensure modal dialog is clickable and interactive with proper z-index */
.modal.show .modal-dialog {
    pointer-events: auto !important;
    position: relative !important;
    z-index: 1051 !important;
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
}

.modal.show .modal-content {
    pointer-events: auto !important;
    position: relative !important;
    z-index: 1052 !important;
    background-color: #fff !important;
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.3rem !important;
}

.modal.show .modal-content * {
    pointer-events: auto !important;
    color: #000 !important;
}

/* Ensure modal header and body are visible */
.modal.show .modal-header,
.modal.show .modal-body,
.modal.show .modal-footer {
    background-color: #fff !important;
    color: #000 !important;
}

/* Force modal content to be visible with maximum priority */
#uploadModal0 .modal-content,
#uploadModal1 .modal-content,
#uploadModal2 .modal-content,
#uploadModal3 .modal-content,
#uploadModal4 .modal-content,
#uploadModal5 .modal-content,
#uploadModal6 .modal-content,
#uploadModal7 .modal-content,
#uploadModal8 .modal-content,
#uploadModal9 .modal-content {
    background-color: #ffffff !important;
    color: #000000 !important;
    z-index: 9999 !important;
    position: relative !important;
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}

#uploadModal0 .modal-dialog,
#uploadModal1 .modal-dialog,
#uploadModal2 .modal-dialog,
#uploadModal3 .modal-dialog,
#uploadModal4 .modal-dialog,
#uploadModal5 .modal-dialog,
#uploadModal6 .modal-dialog,
#uploadModal7 .modal-dialog,
#uploadModal8 .modal-dialog,
#uploadModal9 .modal-dialog {
    z-index: 9998 !important;
    position: relative !important;
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
}
</style>

@push('scripts')
<script>
// Ensure Bootstrap modals work with jQuery - wait for full page load
(function() {
    function initModals() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.modal === 'undefined') {
            console.log('jQuery or Bootstrap modal not ready, retrying...');
            setTimeout(initModals, 100);
            return;
        }
        
        var $ = jQuery;
        console.log('Initializing upload modals');
        
        // Handle all buttons with data-toggle="modal"
        $(document).off('click', '[data-toggle="modal"]').on('click', '[data-toggle="modal"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $button = $(this);
            var target = $button.attr('data-target') || $button.data('target');
            
            if (target) {
                console.log('Opening modal:', target);
                var $modal = $(target);
                
                if ($modal.length === 0) {
                    console.error('Modal not found:', target);
                    alert('Modal not found. Please refresh the page.');
                    return;
                }
                
                console.log('Modal found, element:', $modal[0]);
                console.log('Modal classes:', $modal.attr('class'));
                
                // Remove any existing modal instances
                $modal.removeData('bs.modal');
                
                // Try to show modal
                try {
                    // Ensure modal has proper positioning and z-index
                    $modal.css({
                        'position': 'fixed',
                        'top': '0',
                        'left': '0',
                        'z-index': '1050',
                        'width': '100%',
                        'height': '100%',
                        'overflow-x': 'hidden',
                        'overflow-y': 'auto',
                        'display': 'block',
                        'opacity': '1',
                        'visibility': 'visible'
                    });
                    
                    // First, ensure modal itself is visible BEFORE calling modal('show')
                    $modal.addClass('show');
                    $modal.css({
                        'display': 'block',
                        'opacity': '1',
                        'visibility': 'visible',
                        'position': 'fixed',
                        'top': '0',
                        'left': '0',
                        'z-index': '1050'
                    });
                    $modal.removeAttr('aria-hidden');
                    $modal.attr('aria-modal', 'true');
                    
                    // Use Bootstrap 4 modal method
                    $modal.modal('show');
                    console.log('Modal show called');
                    console.log('Modal has show class:', $modal.hasClass('show'));
                    
                    // Ensure modal dialog is visible and positioned correctly
                    setTimeout(function() {
                        // Ensure modal still has show class
                        if (!$modal.hasClass('show')) {
                            $modal.addClass('show');
                        }
                        
                        var $dialog = $modal.find('.modal-dialog');
                        if ($dialog.length > 0) {
                            // Remove flexbox behavior that might be causing height issues
                            $dialog.css({
                                'position': 'relative',
                                'z-index': '1051',
                                'margin': '1.75rem auto',
                                'max-width': '500px',
                                'width': '90%',
                                'display': 'flex',
                                'flex-direction': 'column',
                                'align-items': 'center',
                                'justify-content': 'center',
                                'min-height': 'auto',
                                'height': 'auto',
                                'pointer-events': 'auto',
                                'opacity': '1',
                                'visibility': 'visible',
                                'transform': 'none'
                            });
                            
                            // Remove the ::before pseudo-element height by overriding it
                            $dialog.css('min-height', 'auto');
                            $dialog[0].style.setProperty('min-height', 'auto', 'important');
                            
                            console.log('Modal dialog positioned');
                            console.log('Dialog visibility:', $dialog.is(':visible'));
                            console.log('Dialog height:', $dialog.height());
                            console.log('Dialog position:', $dialog.css('position'));
                            console.log('Dialog opacity:', $dialog.css('opacity'));
                            console.log('Dialog top:', $dialog.offset().top);
                            console.log('Dialog left:', $dialog.offset().left);
                            console.log('Dialog computed height:', window.getComputedStyle($dialog[0]).height);
                        }
                        
                        // Ensure modal content is visible
                        var $content = $modal.find('.modal-content');
                        if ($content.length > 0) {
                            $content.css({
                                'display': 'block',
                                'position': 'relative',
                                'z-index': '1052',
                                'pointer-events': 'auto',
                                'opacity': '1',
                                'visibility': 'visible'
                            });
                            console.log('Modal content positioned');
                            console.log('Content visibility:', $content.is(':visible'));
                            console.log('Content height:', $content.height());
                            console.log('Content opacity:', $content.css('opacity'));
                        }
                        
                        // Ensure backdrop is behind modal (z-index 1040)
                        // Remove any duplicate backdrops
                        $('.modal-backdrop').not(':first').remove();
                        
                        $('.modal-backdrop').css({
                            'z-index': '1040',
                            'opacity': '0.5',
                            'pointer-events': 'auto'
                        });
                        
                        // Ensure backdrop doesn't cover modal content
                        $('.modal-backdrop').css('z-index', '1040');
                        
                        // Set modal to be visible and clickable
                        $modal.css({
                            'z-index': '1050',
                            'pointer-events': 'auto', // Modal catches clicks
                            'display': 'block',
                            'opacity': '1',
                            'visibility': 'visible'
                        });
                        
                        // Force modal dialog to be on top and clickable (z-index 1051)
                        $dialog.css({
                            'z-index': '1051',
                            'pointer-events': 'auto', // Dialog catches clicks
                            'position': 'relative',
                            'display': 'flex',
                            'opacity': '1',
                            'visibility': 'visible'
                        });
                        
                        // Force modal content to be on top and clickable (z-index 1052) with white background
                        $content.css({
                            'z-index': '1052',
                            'pointer-events': 'auto', // Content catches clicks
                            'position': 'relative',
                            'background-color': '#fff',
                            'display': 'block',
                            'opacity': '1',
                            'visibility': 'visible',
                            'border': '1px solid #dee2e6',
                            'border-radius': '0.3rem'
                        });
                        
                        // Ensure all text is visible
                        $content.find('*').css('color', '#000');
                        
                        // Ensure all form elements are clickable
                        $content.find('input, button, textarea, select, label').css('pointer-events', 'auto');
                        
                        // Ensure modal is scrollable and scroll to top
                        $modal.scrollTop(0);
                        
                        // Make sure dialog is visible by checking if it's in viewport
                        var dialogTop = $dialog.offset().top;
                        var viewportHeight = $(window).height();
                        console.log('Dialog top:', dialogTop, 'Viewport height:', viewportHeight);
                        
                        // Scroll modal to top
                        $modal.scrollTop(0);
                        
                        // Also scroll window to show modal
                        var windowScrollTop = $(window).scrollTop();
                        console.log('Window scroll top:', windowScrollTop);
                        
                        // Force modal to be visible by ensuring it's in the viewport
                        $('html, body').scrollTop(0);
                        
                        console.log('Modal fully initialized');
                        console.log('Modal has show class:', $modal.hasClass('show'));
                        console.log('Modal display:', $modal.css('display'));
                        console.log('Modal opacity:', $modal.css('opacity'));
                        console.log('Modal visibility:', $modal.css('visibility'));
                        console.log('Backdrop z-index:', $('.modal-backdrop').css('z-index'));
                        console.log('Dialog z-index:', $dialog.css('z-index'));
                        console.log('Content z-index:', $content.css('z-index'));
                        console.log('Modal scrollTop:', $modal.scrollTop());
                        console.log('Window scrollTop:', $(window).scrollTop());
                        
                        // Final check - try to make modal more visible with explicit styles
                        $modal[0].style.setProperty('display', 'block', 'important');
                        $modal[0].style.setProperty('opacity', '1', 'important');
                        $modal[0].style.setProperty('visibility', 'visible', 'important');
                        $modal[0].style.setProperty('z-index', '1050', 'important');
                        
                        // Ensure dialog is visible
                        if ($dialog.length > 0) {
                            $dialog[0].style.setProperty('display', 'flex', 'important');
                            $dialog[0].style.setProperty('opacity', '1', 'important');
                            $dialog[0].style.setProperty('visibility', 'visible', 'important');
                            $dialog[0].style.setProperty('z-index', '1051', 'important');
                            $dialog[0].style.setProperty('background-color', 'transparent', 'important');
                        }
                        
                        // Ensure content is visible with white background - use maximum z-index
                        if ($content.length > 0) {
                            $content[0].style.setProperty('display', 'block', 'important');
                            $content[0].style.setProperty('opacity', '1', 'important');
                            $content[0].style.setProperty('visibility', 'visible', 'important');
                            $content[0].style.setProperty('z-index', '9999', 'important');
                            $content[0].style.setProperty('background-color', '#ffffff', 'important');
                            $content[0].style.setProperty('border', '1px solid #dee2e6', 'important');
                            $content[0].style.setProperty('pointer-events', 'auto', 'important');
                            $content[0].style.setProperty('position', 'relative', 'important');
                            $content[0].style.setProperty('color', '#000000', 'important');
                            
                            // Also set background on child elements
                            $content.find('.modal-header, .modal-body, .modal-footer').css({
                                'background-color': '#fff',
                                'color': '#000'
                            });
                        }
                        
                        // Also ensure dialog has proper background and high z-index
                        if ($dialog.length > 0) {
                            $dialog[0].style.setProperty('background-color', 'transparent', 'important');
                            $dialog[0].style.setProperty('pointer-events', 'auto', 'important');
                            $dialog[0].style.setProperty('z-index', '9998', 'important');
                            $dialog[0].style.setProperty('position', 'relative', 'important');
                        }
                        
                        // Ensure modal itself has high z-index
                        $modal[0].style.setProperty('z-index', '9997', 'important');
                        
                        // Double-check backdrop is behind and move it before modal if needed
                        var $backdrop = $('.modal-backdrop').first();
                        if ($backdrop.length > 0) {
                            // Ensure backdrop z-index is lower
                            $backdrop[0].style.setProperty('z-index', '1040', 'important');
                            $backdrop[0].style.setProperty('pointer-events', 'auto', 'important');
                            
                            // Move backdrop BEFORE modal in DOM to ensure proper stacking
                            // This ensures backdrop renders first, then modal renders on top
                            if ($backdrop.next().length === 0 || !$backdrop.next().is($modal)) {
                                $backdrop.insertBefore($modal);
                            }
                        }
                        
                        // Ensure modal is in body (it should be)
                        if ($modal.parent().length > 0 && !$modal.parent().is('body')) {
                            $modal.detach().appendTo('body');
                        }
                        
                        // Force modal content to be visible with inline styles on all elements
                        if ($content.length > 0) {
                            // Set styles directly on the element - use direct property assignment
                            var contentEl = $content[0];
                            contentEl.style.setProperty('z-index', '9999', 'important');
                            contentEl.style.setProperty('background-color', '#ffffff', 'important');
                            contentEl.style.setProperty('color', '#000000', 'important');
                            contentEl.style.setProperty('position', 'relative', 'important');
                            contentEl.style.setProperty('display', 'block', 'important');
                            contentEl.style.setProperty('opacity', '1', 'important');
                            contentEl.style.setProperty('visibility', 'visible', 'important');
                            contentEl.style.setProperty('border', '1px solid #dee2e6', 'important');
                            contentEl.style.setProperty('border-radius', '0.3rem', 'important');
                            contentEl.style.setProperty('box-shadow', '0 0.5rem 1rem rgba(0, 0, 0, 0.15)', 'important');
                            contentEl.style.setProperty('pointer-events', 'auto', 'important');
                            
                            // Also set on header, body, footer - use direct style assignment
                            var header = $content.find('.modal-header')[0];
                            var body = $content.find('.modal-body')[0];
                            var footer = $content.find('.modal-footer')[0];
                            
                            if (header) {
                                header.style.setProperty('background-color', '#fff', 'important');
                                header.style.setProperty('color', '#000', 'important');
                            }
                            if (body) {
                                body.style.setProperty('background-color', '#fff', 'important');
                                body.style.setProperty('color', '#000', 'important');
                            }
                            if (footer) {
                                footer.style.setProperty('background-color', '#fff', 'important');
                                footer.style.setProperty('color', '#000', 'important');
                            }
                        }
                        
                        console.log('Modal forced to be visible with inline styles');
                        console.log('Backdrop count:', $('.modal-backdrop').length);
                        console.log('Backdrop z-index:', $('.modal-backdrop').first().css('z-index'));
                        console.log('Modal z-index:', $modal.css('z-index'));
                        console.log('Content z-index:', $content.css('z-index'));
                        console.log('Content background:', $content.css('background-color'));
                        console.log('Content computed background:', window.getComputedStyle($content[0]).backgroundColor);
                        console.log('Content offset:', $content.offset());
                        console.log('Content width:', $content.width());
                        console.log('Content height:', $content.height());
                    }, 200);
                } catch (err) {
                    console.error('Error showing modal:', err);
                    // Fallback: manual show
                    $modal.addClass('show');
                    $modal.css('display', 'block');
                    $modal.css('z-index', '1050');
                    $modal.attr('aria-hidden', 'false');
                    $modal.removeAttr('aria-hidden');
                    $('body').addClass('modal-open');
                    
                    // Ensure modal dialog is visible
                    var $dialog = $modal.find('.modal-dialog');
                    $dialog.css('position', 'relative');
                    $dialog.css('z-index', '1051');
                    $dialog.css('margin', '1.75rem auto');
                    
                    if ($('.modal-backdrop').length === 0) {
                        $('body').append('<div class="modal-backdrop fade show" style="z-index: 1040;"></div>');
                    }
                }
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModals);
    } else {
        initModals();
    }
})();
</script>
@endpush

@endsection

