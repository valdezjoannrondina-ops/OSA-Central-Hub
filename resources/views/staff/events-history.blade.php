@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-4">Event History</h1>
    <form method="GET" action="{{ route('staff.events.history') }}" class="mb-6 flex gap-2 items-center">
        <input type="date" name="date" value="{{ request('date') }}" class="border rounded px-2 py-1" />
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($events as $event)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $event->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $event->event_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $event->location }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $event->description }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No events found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection
