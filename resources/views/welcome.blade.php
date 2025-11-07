@extends('layouts.app')

@section('title', 'OSA Central Hub - USTP Balubal')

@section('content')

@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<!-- Top contact bar for welcome page only -->
<div class="topbar" role="banner">
    <div class="container">
        <div class="d-flex justify-content-center py-2">
            <div class="site-info">
                <a href="tel:+6312344556666" class="me-3">
                    <span class="mai-call text-primary" aria-hidden="true"></span>
                    <span class="visually-hidden">Call:</span>
                    +63 123 4455 6666
                </a>
                <a href="mailto:osa.balubal@ustp.edu.ph">
                    <span class="mai-mail text-primary" aria-hidden="true"></span>
                    <span class="visually-hidden">Email:</span>
                    osa.balubal@ustp.edu.ph
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-hero bg-image overlay-dark" style="background-image: url('{{ asset('assets/img/bg_image_1.jpg') }}');">
    <div class="hero-section">
        <div class="container text-center wow zoomIn">
            <span class="subhead">Breaking Limits</span>
            <h1 class="display-4">OSA Central Hub</h1>
        </div>
    </div>
</div>


@include('welcome-osa')
@include('show-staff')
@include('upcoming-events')
@include('make-appointment')

{{-- Registration is disabled. Remove any registration links or forms. --}}

@endsection