@extends('layouts.app')

@section('title', 'Create Event')

@section('content')
<div class="container-fluid">
  <main class="col-12">
    <div class="card mb-3">
      <div class="card-body">
        <h2 class="mb-3">Create Event (Student Org. Moderator)</h2>
        <form action="{{ route('admin.staff.dashboard.StudentOrgModerator.event.store') }}" method="POST">


            <main class="col-12">
              <div class="card mb-3">
                <div class="container-fluid">
                  <div class="row">
                    <!-- Sidebar -->
                    <div class="col-md-3 col-lg-2">
                      <div class="card mb-4">
                        <div class="card-body">
                          <h5 class="mb-3">Navigation</h5>
                          <a href="{{ route('admin.staff.dashboard.StudentOrgModerator.view-events') }}" class="btn btn-outline-success w-100 mb-2">Back to Event List</a>
                          <a href="{{ route('admin.staff.dashboard.StudentOrgModerator.create-event') }}" class="btn btn-outline-success w-100">Create New Event</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-9 col-lg-10">
                      <div class="card">
                        <div class="card-header d-flex align-items-center" style="background: #00d6b2; color: #fff;">
                          <h3 class="mb-0" style="font-weight: 500;">Create Event</h3>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-8">
                              <form action="{{ route('admin.staff.dashboard.StudentOrgModerator.event.store') }}" method="POST">
                                @csrf
                                <table class="table table-borderless">
                                  <tr>
                                    <td><label for="title">Name</label></td>
                                    <td><input type="text" name="title" id="title" class="form-control" required></td>
                                  </tr>
                                  <tr>
                                    <td><label for="event_date">Date Started</label></td>
                                    <td><input type="date" name="event_date" id="event_date" class="form-control" required></td>
                                  </tr>
                                  <tr>
                                    <td><label for="end_date">Date Ended</label></td>
                                    <td><input type="date" name="end_date" id="end_date" class="form-control"></td>
                                  </tr>
                                  <tr>
                                    <td><label for="start_time">Time Started</label></td>
                                    <td><input type="time" name="start_time" id="start_time" class="form-control" required></td>
                                  </tr>
                                  <tr>
                                    <td><label for="end_time">Time Ended</label></td>
                                    <td><input type="time" name="end_time" id="end_time" class="form-control" required></td>
                                  </tr>
                                  <tr>
                                    <td><label for="location">Location</label></td>
                                    <td><input type="text" name="location" id="location" class="form-control" required></td>
                                  </tr>
                                  <tr>
                                    <td><label for="organization_id">Organization</label></td>
                                    <td>
                                      <select name="organization_id" id="organization_id" class="form-control" required>
                                        <option value="">Select Organization*</option>
                                        @foreach($organizations as $org)
                                          <option value="{{ $org->id }}">{{ $org->name }}</option>
                                        @endforeach
                                      </select>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td><label for="description">Description</label></td>
                                    <td><textarea name="description" id="description" class="form-control" rows="3"></textarea></td>
                                  </tr>
                                </table>
                                <button type="submit" class="btn" style="background: #ffe600; color: #222; min-width: 100px;">Create Event</button>
                              </form>
                            </div>
                            <div class="col-md-4 d-flex flex-column align-items-center justify-content-center">
                              <h5 class="mb-3">QR Code for Attendance</h5>
                              <span style="color: #ff4d4d; margin-bottom: 1rem;">QR code will be generated after event is created.</span>
                              <button class="btn" style="background: #00d6b2; color: #fff; min-width: 180px;" disabled>Open Camera to Scan</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </main>
          </div>
