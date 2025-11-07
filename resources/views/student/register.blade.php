@extends('layouts.app')

@section('title', 'Student Registration')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register as Student') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student.register') }}">
                        @csrf
                        <!-- Student Type 1 -->
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Student Type') }}</label>
                            <div class="col-md-6">
                                <select name="student_type1" class="form-control" required>
                                    <option value="regular" {{ old('student_type1') == 'regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="irregular" {{ old('student_type1') == 'irregular' ? 'selected' : '' }}>Irregular</option>
                                    <option value="transferee" {{ old('student_type1') == 'transferee' ? 'selected' : '' }}>Transferee</option>
                                </select>
                            </div>
                        </div>
                        <!-- Student Type 2 -->
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Payment Type') }}</label>
                            <div class="col-md-6">
                                <select id="student_type2" name="student_type2" class="form-control" required>
                                    <option value="paying" {{ old('student_type2') == 'paying' ? 'selected' : '' }}>Paying</option>
                                    <option value="scholar" {{ old('student_type2') == 'scholar' ? 'selected' : '' }}>Scholar</option>
                                </select>
                            </div>
                        </div>
                        <!-- Scholarship (appears only if scholar) -->
                        <div class="row mb-3" id="scholarship-field" style="display:none;">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Scholarship') }}</label>
                            <div class="col-md-6">
                                <select name="scholarship_id" class="form-control">
                                    <option value="">Select Scholarship (Optional)</option>
                                    @foreach($scholarships as $scholarship)
                                        <option value="{{ $scholarship->id }}">{{ $scholarship->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Contact Info -->
                        <div class="row mb-3">
                            <label for="contact_number" class="col-md-4 col-form-label text-md-end">{{ __('Contact Number') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="contact_number" value="{{ old('contact_number') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="emergency_contact_name" class="col-md-4 col-form-label text-md-end">{{ __('Emergency Contact Person') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="emergency_contact_number" class="col-md-4 col-form-label text-md-end">{{ __('Emergency Contact Number') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Relation to contact Person') }}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="emergency_relation" value="{{ old('emergency_relation') }}" required>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const type2 = document.getElementById('student_type2');
    const scholarshipField = document.getElementById('scholarship-field');
    if (type2 && scholarshipField) {
        type2.addEventListener('change', () => {
            scholarshipField.style.display = type2.value === 'scholar' ? 'block' : 'none';
        });
        if (type2.value === 'scholar') scholarshipField.style.display = 'block';
    }
});
</script>
@endpush

