@extends('layouts.app')

@section('title', 'My Organization Files')

@section('content')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-12">
      <a href="{{ route('staff.organizations.index') }}" class="btn btn-secondary">&larr; Back to Organizations</a>
      <a href="{{ route('staff.organization-files.create', $organization->id) }}" class="btn btn-primary float-end">
        <i class="bi bi-cloud-upload"></i> Upload File
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 col-lg-2">
      @include('staff.partials.sidebar')
    </div>
    
    <main class="col-md-9 col-lg-10">
      <div class="card">
        <div class="card-header" style="background-color: midnightblue; color: white;">
          <h4 class="mb-0">
            <i class="bi bi-folder"></i> My Files - {{ $organization->name }}
          </h4>
          @if($organization->department)
            <small>{{ $organization->department->name }} - Academic Organization</small>
          @else
            <small>Non-Academic Organization</small>
          @endif
        </div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Success!</strong> {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @if($files->isEmpty())
            <div class="alert alert-info">
              <p class="mb-0">No files uploaded yet. <a href="{{ route('staff.organization-files.create', $organization->id) }}">Upload your first file</a></p>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>File Name</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Size</th>
                    <th>Uploaded By</th>
                    <th>Uploaded At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($files as $file)
                    <tr>
                      <td>
                        <i class="bi bi-file-earmark"></i>
                        {{ $file->file_name }}
                      </td>
                      <td>
                        @php
                          $fileTypeLabels = [
                            'personal_data_sheet' => 'Personal Data Sheet',
                            'image' => 'Image',
                            'pdf' => 'PDF',
                            'document' => 'Document',
                            'spreadsheet' => 'Spreadsheet',
                            'other' => 'Other'
                          ];
                          $typeLabel = $fileTypeLabels[$file->file_type] ?? ucfirst($file->file_type ?? 'Other');
                        @endphp
                        <span class="badge bg-secondary">{{ $typeLabel }}</span>
                      </td>
                      <td>{{ $file->description ?? '-' }}</td>
                      <td>{{ $file->human_readable_size }}</td>
                      <td>{{ $file->uploader->first_name ?? '' }} {{ $file->uploader->last_name ?? '' }}</td>
                      <td>{{ $file->created_at->format('M d, Y g:i A') }}</td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="{{ route('staff.organization-files.download', [$organization->id, $file->id]) }}" class="btn btn-sm btn-primary" title="Download">
                            <i class="bi bi-download"></i>
                          </a>
                          <form action="{{ route('staff.organization-files.destroy', [$organization->id, $file->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      <!-- Folder Structure Info -->
      <div class="card mt-3">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-info-circle"></i> File Storage</h5>
          <p class="card-text">
            <strong>Storage Location:</strong> <code>storage/app/public/staff/{{ auth()->id() }}/organizations/{{ $organization->id }}/</code>
          </p>
          <p class="card-text mb-0">
            Files are organized in folders by staff member and organization. Each staff member has their own folder for each organization they handle.
          </p>
        </div>
      </div>
    </main>
  </div>
</div>
@endsection
