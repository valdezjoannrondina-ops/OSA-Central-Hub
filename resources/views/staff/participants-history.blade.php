@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-4">Event Participants History</h1>
    <form method="GET" action="{{ route('staff.participants.history') }}" class="mb-6 flex gap-2 items-center">
        <select name="event_id" class="border rounded px-2 py-1">
            <option value="">All Events</option>
            @foreach ($events as $event)
                <option value="{{ $event->id }}" @selected(request('event_id') == $event->id)>{{ $event->title }}</option>
            @endforeach
        </select>
        <select name="department_id" class="border rounded px-2 py-1">
            <option value="">All Departments</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
            @endforeach
        </select>
        <select name="course_id" class="border rounded px-2 py-1">
            <option value="">All Courses</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->name }}</option>
            @endforeach
        </select>
        <select name="year_level" class="border rounded px-2 py-1">
            <option value="">All Years</option>
            @for ($i = 1; $i <= 5; $i++)
                <option value="{{ $i }}" @selected(request('year_level') == $i)>Year {{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Filter</button>
    </form>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($participants as $p)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $p->user->first_name }} {{ $p->user->last_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $p->event->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $p->user->department->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $p->user->course->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $p->user->year_level ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No participants found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $participants->links() }}
    </div>
</div>
@endsection
