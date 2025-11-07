<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #EED818;
            color: #333;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .details {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #EED818;
        }
        .old-details {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #6c757d;
        }
        .reason {
            background-color: #fff3cd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border: 1px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Appointment Rescheduled</h2>
        </div>
        <div class="content">
            <p>Dear {{ $appointment->full_name }},</p>
            
            <p>Your appointment has been <strong>rescheduled</strong> to a new date and time.</p>
            
            <div class="old-details">
                <p><strong>Original Appointment:</strong></p>
                <p><strong>Date:</strong> {{ $appointment->created_at->format('F d, Y') }}</p>
            </div>
            
            <div class="details">
                <p><strong>New Appointment Details:</strong></p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->rescheduled_date ?? $appointment->appointment_date)->format('F d, Y') }}</p>
                <p><strong>Time:</strong> {{ $appointment->rescheduled_time ? date('g:i A', strtotime($appointment->rescheduled_time)) : ($appointment->appointment_time ? date('g:i A', strtotime($appointment->appointment_time)) : 'To be determined') }}</p>
                @if($appointment->concern)
                <p><strong>Concern:</strong> {{ $appointment->concern }}</p>
                @endif
            </div>
            
            @if($appointment->action_reason)
            <div class="reason">
                <p><strong>Reason for Rescheduling:</strong></p>
                <p>{{ $appointment->action_reason }}</p>
            </div>
            @endif
            
            <p>Please note this new appointment date and time. <strong>OSA Balubal is looking forward to meeting you personally</strong> on the rescheduled date.</p>
            
            <p>If this new schedule is not convenient for you, please contact us as soon as possible to discuss alternative arrangements.</p>
            
            <p>Thank you for your understanding and flexibility.</p>
            
            <p>Best regards,<br>
            <strong>OSA Balubal Team</strong></p>
        </div>
    </div>
</body>
</html>
