<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdminQrScanController extends Controller
{
    /**
     * Scan QR code and save as event participation
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        try {
            // Parse QR code data (JSON format)
            $qrData = json_decode($request->qr_data, true);
            
            if (!$qrData) {
                // Try to parse as string format if JSON fails
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format. Please scan a valid student QR code.'
                ], 400);
            }

            // Extract student information from QR data
            // QR code can have student_id (user_id) or id
            $studentId = $qrData['student_id'] ?? $qrData['id'] ?? null;
            $firstName = $qrData['first_name'] ?? null;
            $lastName = $qrData['last_name'] ?? null;

            if (!$studentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student ID not found in QR code.'
                ], 400);
            }

            // Find user by user_id field first (student_id in QR)
            $user = User::where('user_id', $studentId)->where('role', 1)->first();
            
            // If not found, try by id field (if student_id is actually the user's id)
            if (!$user) {
                $user = User::where('id', $studentId)->where('role', 1)->first();
            }
            
            // Fallback: try to find by name if available
            if (!$user && $firstName && $lastName) {
                $user = User::where('first_name', $firstName)
                    ->where('last_name', $lastName)
                    ->where('role', 1) // student
                    ->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found in database. Please ensure the student is registered.'
                ], 404);
            }

            // Verify event exists and is accessible
            $event = Event::findOrFail($request->event_id);

            // Check if participation already exists
            $existingParticipation = EventParticipant::where('event_id', $event->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingParticipation) {
                // Update existing participation with QR scan info
                $existingParticipation->update([
                    'qr_scanned' => true,
                    'scanned_at' => now(),
                    'scanned_by' => auth()->id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "{$user->first_name} {$user->last_name} is already registered for this event. QR scan updated.",
                    'student' => [
                        'id' => $user->id,
                        'name' => "{$user->first_name} {$user->last_name}",
                        'student_id' => $user->user_id,
                    ],
                    'event' => [
                        'id' => $event->id,
                        'title' => $event->title ?? $event->name,
                    ],
                ]);
            }

            // Create new participation record
            $participation = EventParticipant::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'qr_scanned' => true,
                'scanned_at' => now(),
                'scanned_by' => auth()->id(),
            ]);

            Log::info('QR code scanned for event participation', [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'scanned_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$user->first_name} {$user->last_name} has been registered for {$event->title ?? $event->name}",
                'student' => [
                    'id' => $user->id,
                    'name' => "{$user->first_name} {$user->last_name}",
                    'student_id' => $user->user_id,
                ],
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title ?? $event->name,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('QR scan error', [
                'error' => $e->getMessage(),
                'qr_data' => $request->qr_data,
                'event_id' => $request->event_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the QR code: ' . $e->getMessage()
            ], 500);
        }
    }
}

