<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    // PUT /student/appointments/{id}/reschedule
    public function reschedule($id, Request $request)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'message' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'message' => $request->message,
            'status' => 'pending', // Reset status for re-approval; staff must re-approve
        ]);

        \App\Models\StatusChange::create([
            'auditable_type' => \App\Models\Appointment::class,
            'auditable_id' => $appointment->id,
            'from_status' => 'approved',
            'to_status' => 'pending',
            'changed_by' => auth()->id(),
            'meta' => ['reason' => $request->message],
        ]);

        return back()->with('success', 'Appointment rescheduled and pending approval.');
    }
}
