@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-4">Request an Appointment</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('appointments.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium">Full name</label>
            <input type="text" name="full_name" value="{{ old('full_name') }}" class="mt-1 w-full border rounded px-3 py-2" required />
        </div>

        <div>
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded px-3 py-2" required />
        </div>

        <div>
            <label class="block text-sm font-medium">Contact number</label>
            <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="mt-1 w-full border rounded px-3 py-2" required />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Appointment date</label>
                <input type="date" name="appointment_date" value="{{ old('appointment_date') }}" class="mt-1 w-full border rounded px-3 py-2" required />
            </div>
            <div>
                <label class="block text-sm font-medium">Time</label>
                <input type="time" name="appointment_time" value="{{ old('appointment_time') }}" class="mt-1 w-full border rounded px-3 py-2" required />
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Concern</label>
            <select name="concern" class="mt-1 w-full border rounded px-3 py-2" required>
                <option value="" disabled selected>Select a concern</option>
                @foreach ($concerns as $c)
                    <option value="{{ $c }}" @selected(old('concern') === $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Message (optional)</label>
            <textarea name="message" rows="4" class="mt-1 w-full border rounded px-3 py-2">{{ old('message') }}</textarea>
        </div>

        <div class="pt-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Submit request</button>
        </div>
    </form>
</div>
@endsection
