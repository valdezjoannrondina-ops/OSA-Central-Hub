<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    // PUT /staff/appointments/{id}/reschedule
    public function reschedule($id, Request $request)
    {
        $appointment = Appointment::where('id', $id)
            ->where('assigned_staff_id', auth()->id())
            ->where('status', 'approved')
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending', // Reset status for re-approval
        ]);

        return back()->with('success', 'Appointment rescheduled and pending approval.');
    }
}
