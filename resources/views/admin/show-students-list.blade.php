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
            .students-list-title {
                display: block;
                width: 100%;
                box-sizing: border-box;
                background-color: #ffffff;
                color: midnightblue;
                padding: .5rem 1rem; /* align with other header boxes */
                border: none;            /* remove all borders */
                border-bottom: 1px solid midnightblue; /* keep only bottom border in navy */
                border-radius: 0;        /* remove rounding for underline style */
            }
        </style>
        <h2 class="mb-3"><span class="students-list-title">Students List</span></h2>
        <form method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-auto">
                <label for="department_id" class="form-label">Department</label>
                <select name="department_id" id="department_id" class="form-select">
                    <option value="">All</option>
                    @isset($departments)
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (isset($filters['department_id']) && (string)$filters['department_id']===(string)$dept->id) ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="col-auto">
                <label for="course_id" class="form-label">Course</label>
                <select name="course_id" id="course_id" class="form-select">
                    <option value="">All</option>
                    @isset($courses)
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ (isset($filters['course_id']) && (string)$filters['course_id']===(string)$course->id) ? 'selected' : '' }}>{{ $course->name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="col-auto">
                <label for="year_level" class="form-label">Year Level</label>
                <input type="text" name="year_level" id="year_level" class="form-control" value="{{ $filters['year_level'] ?? '' }}" placeholder="e.g. 1, 2, 3, 4">
            </div>
            <div class="col-auto">
                <label for="status" class="form-label">Status</label>
                <input type="text" name="status" id="status" class="form-control" value="{{ $filters['status'] ?? '' }}" placeholder="e.g. active">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.show-students-list') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr class="text-center" style="background-color:midnightblue; color:white">
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr class="text-center">
                        <td>{{ $student->user ? $student->user->user_id : '-' }}</td>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->department->name ?? '' }}</td>
                        <td>{{ $student->course->name ?? '-' }}</td>
                        <td>{{ $student->year_level ?? '-' }}</td>
                        <td>{{ $student->status ?? '-' }}</td>
                        <td>
                            <!-- Action buttons here -->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $students->links() }}
        </div>
        </main>
    </div>
</div>
@endsection
