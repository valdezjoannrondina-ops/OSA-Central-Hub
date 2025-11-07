@extends('layouts.app')

@section('title', 'Show Staff')

@section('content')
  <style>
    .staff-thumb {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 4px;
    }
  </style>
  <div class="container-fluid">
    <div class="row">
      @include('admin.partials.sidebar')
  <main class="col-md-10">
        <div class="admin-back-btn-wrap">
          <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill px-3">&lt; Back to Dashboard</a>
        </div>
        <style>
          /* Header underline style retained */
          .section-header { display:block; width:100%; box-sizing:border-box; background:#fff; color: midnightblue; padding:.5rem 1rem; border:none; border-bottom:1px solid midnightblue; border-radius:0; }
          /* Scrollable container for the staff table (vertical and horizontal as needed) */
          .table-scroll { max-height: 70vh; overflow: auto; }
          /* Allow horizontal scrolling when columns exceed viewport, but keep at least container width */
          .table-scroll table { width: max-content; min-width: 100%; }
          /* White rectangular background to visually separate table area */
          .staff-table-card { background:#ffffff; border:1px solid rgba(0,0,0,0.08); border-radius:.375rem; padding:1rem; }
          /* Slightly smaller table for comfortable fit */
          .staff-table { font-size: .93rem; }
          .staff-table th, .staff-table td { padding: .5rem .75rem; }
          /* Make rows from row 2 onward white; keep the first row (header) styling intact */
          .staff-table tbody tr + tr { background-color: #ffffff; color: #000; }
        </style>
        <h3 class="mt-4"><span class="section-header">Staff</span></h3>
        <div class="staff-table-card">
        <div class="table-scroll">
        <table class="table table-bordered staff-table align-middle mb-0">
    <tr align="center" style="background-color:midnightblue; color:white">
  <th>Image</th><th>Employee_ID</th><th>First Name</th><th>Middle Name</th><th>Last Name</th><th>Contact Number</th><th>Email</th><th>Designation</th><th>Organizations</th><th>Birth Date</th><th>Gender</th><th>Age</th><th>Contract Ends</th><th>Time Left</th><th>Service Order</th><th>Status</th><th>Delete</th><th>Update</th>
    </tr>
    @foreach($staff as $staff)
      <tr id="staff-{{ $staff->id }}" align="left">
  <td>
    @if(!empty($staff->image))
      <img class="staff-thumb" src="{{ \Illuminate\Support\Facades\Storage::url($staff->image) }}" alt="{{ $staff->first_name }} {{ $staff->last_name }}">
    @else
      <span class="text-muted">No image</span>
    @endif
  </td>
  <td>{{ $staff->user_id ?? $staff->id }}</td>
  <td>{{$staff->first_name}}</td>
  <td>{{$staff->middle_name ?? ''}}</td>
  <td>{{$staff->last_name}}</td>
  <td>{{$staff->contact_number ?? ''}}</td>
  <td>{{$staff->email}}</td>
  <td>{{$staff->designation ?? ''}}</td>
  <td>
    @if($staff->organizations && $staff->organizations->count())
      <div style="display: flex; flex-direction: column; gap: 4px;">
        @foreach($staff->organizations as $org)
          <span style="display: block; width: fit-content; color: black; background: none;">{{ $org->name }}</span>
        @endforeach
      </div>
    @else
      <span class="text-muted">None</span>
    @endif
  </td>
  <td>{{$staff->birth_date ?? '-'}}</td>
  <td>{{$staff->gender ?? '-'}}</td>
  <td>{{$staff->age ?? '-'}}</td>
  <td>
    @if($staff->contract_end_at)
      {{ \Carbon\Carbon::parse($staff->contract_end_at)->format('Y/m/d/') }}
    @else
      -
    @endif
  </td>
  <td>
    @if($staff->contract_end_at)
      <span class="badge bg-info">{{ \Carbon\Carbon::parse($staff->contract_end_at)->format('Y/m/d/') }}</span>
    @else
      <span class="text-muted">-</span>
    @endif
  </td>
  <td>
    @if(!empty($staff->service_order))
      <a href="{{ \Illuminate\Support\Facades\Storage::url($staff->service_order) }}" target="_blank" class="btn btn-info btn-sm">Download S.O.</a>
    @else
      <span class="text-muted">No S.O.</span>
    @endif
  </td>
  <td>
    @php($st = strtolower($staff->employment_status ?? ''))
    @if($st === 'active')
      <span class="badge bg-success">Active</span>
    @elseif($st === 'inactive')
      <span class="badge bg-secondary">Inactive</span>
    @elseif($st === 'ended')
      <span class="badge bg-danger">Ended</span>
    @else
      <span class="badge bg-light text-dark">-</span>
    @endif
  </td>
        <td>
          <form method="POST" action="{{ route('admin.staff.destroy', $staff->id) }}" style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</button>
          </form>
        </td>
        <td>
          <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-warning btn-sm">Update</a>
        </td>
      </tr>
  @endforeach
  </table>
  </div>
  </div>
  </div>
  
      </main>
    </div>
  </div>
@endsection
@push('scripts')
<script>
// Auto-scroll and highlight the updated staff row if URL has a hash like #staff-123
document.addEventListener('DOMContentLoaded', function(){
  if (location.hash) {
    const el = document.querySelector(location.hash);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      el.classList.add('table-warning');
      setTimeout(()=> el.classList.remove('table-warning'), 3000);
    }
  }
});
</script>
@endpush