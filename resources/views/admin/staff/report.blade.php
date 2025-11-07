@extends('layouts.app')

@section('title', 'Staff Reports')

@section('content')
<div class="container-fluid">
  <div class="row mb-3">
    <div class="col-12">
  <a href="{{ url('/admin/staff/dashboard/AdmissionServicesOfficer') }}" class="btn btn-secondary">&larr; Back</a>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3 col-lg-2">
      @include('admin.staff')
    </div>
    <main class="col-md-9 col-lg-10">
      <h2 class="mb-3">Staff Reports</h2>
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="mb-3" style="cursor:pointer;user-select:none;" onclick="toggleImportedFiles()">
            <span id="importedArrow" style="display:inline-block;transition:transform 0.2s;">&#9654;</span> IMPORTED Files
          </h5>
          <ul id="importedFilesList" class="list-group mb-4" style="display:none;">
            @php
              $updatedFiles = glob(public_path('staff/sidebar/report/updated_worksheet_*.json'));
            @endphp
            @forelse($updatedFiles as $file)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ basename($file) }}
                <div class="btn-group" role="group">
                  <a href="{{ route('admin.staff.dashboard.report.view', ['filename' => urlencode(basename($file))]) }}" class="btn btn-sm btn-info" target="_blank">View</a>
                  <a href="{{ asset('staff/sidebar/report/' . basename($file)) }}" class="btn btn-sm btn-success" download>Download</a>
                  <form method="POST"
                        action="{{ route('admin.staff.dashboard.report.delete', ['filename' => urlencode(basename($file))]) }}"
                        class="delete-form"
                        data-filename="{{ basename($file) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </div>
              </li>
            @empty
              <li class="list-group-item">No updated files found.</li>
            @endforelse
          </ul>
          <script>
            function toggleImportedFiles() {
              var list = document.getElementById('importedFilesList');
              var arrow = document.getElementById('importedArrow');
              if (list.style.display === 'none') {
                list.style.display = '';
                arrow.style.transform = 'rotate(90deg)';
              } else {
                list.style.display = 'none';
                arrow.style.transform = '';
              }
            }
          </script>

          <h5 class="mb-3" style="cursor:pointer;user-select:none;" onclick="toggleUpdatedFiles()">
            <span id="updatedArrow" style="display:inline-block;transition:transform 0.2s;">&#9654;</span> UPDATED Files
          </h5>
          @if(session('success'))
            <div class="alert alert-success">Successfully updated</div>
          @endif
          <ul id="updatedFilesList" class="list-group mb-4" style="display:none;">
            @php
              $updatedFiles = glob(public_path('staff/sidebar/report/updated_worksheet_*.json'));
            @endphp
            @forelse($updatedFiles as $file)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ basename($file) }}
                <div class="btn-group" role="group">
                  <a href="{{ route('admin.staff.dashboard.report.view', ['filename' => urlencode(basename($file))]) }}" class="btn btn-sm btn-info" target="_blank">View</a>
                  <a href="{{ asset('staff/sidebar/report/' . basename($file)) }}" class="btn btn-sm btn-success" download>Download</a>
                  <form method="POST"
                        action="{{ route('admin.staff.dashboard.report.delete', ['filename' => urlencode(basename($file))]) }}"
                        class="delete-form"
                        data-filename="{{ basename($file) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </div>
              </li>
            @empty
              <li class="list-group-item">No updated files found.</li>
            @endforelse
          </ul>
          <script>
            function toggleImportedFiles() {
              var list = document.getElementById('importedFilesList');
              var arrow = document.getElementById('importedArrow');
              if (list.style.display === 'none') {
                list.style.display = '';
                arrow.style.transform = 'rotate(90deg)';
              } else {
                list.style.display = 'none';
                arrow.style.transform = '';
              }
            }
            function toggleUpdatedFiles() {
              var list = document.getElementById('updatedFilesList');
              var arrow = document.getElementById('updatedArrow');
              if (list.style.display === 'none') {
                list.style.display = '';
                arrow.style.transform = 'rotate(90deg)';
              } else {
                list.style.display = 'none';
                arrow.style.transform = '';
              }
            }
            document.addEventListener("DOMContentLoaded", function () {
              document.querySelectorAll(".delete-form").forEach(form => {
                form.addEventListener("submit", function (e) {
                  e.preventDefault();
                  if (!confirm("Delete this file?")) return;
                  let actionUrl = this.getAttribute("action");
                  let token = this.querySelector("input[name=_token]").value;
                  let listItem = this.closest("li");
                  fetch(actionUrl, {
                    method: "POST",
                    headers: {
                      "X-CSRF-TOKEN": token,
                      "X-Requested-With": "XMLHttpRequest"
                    }
                  })
                  .then(response => {
                    if (response.ok) {
                      listItem.remove();
                    } else {
                      alert("Failed to delete file.");
                    }
                  })
                  .catch(() => alert("Error deleting file."));
                });
              });
            });
          </script>

          <!-- <h5 class="mb-3">Other Files (Future)</h5>
          <ul class="list-group">
            <li class="list-group-item">No other files yet.</li>
          </ul> -->
        </div>
      </div>
    </main>
  </div>
</div>
@endsection
