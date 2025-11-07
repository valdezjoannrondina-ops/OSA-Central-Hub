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
            background-color: #FF4943;
            color: white;
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
            border-left: 4px solid #FF4943;
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
            <h2>Appointment Declined</h2>
        </div>
        <div class="content">
            <p>Dear {{ $appointment->full_name }},</p>
            
            <p>We regret to inform you that your appointment request has been <strong>declined</strong>.</p>
            
            <div class="details">
                <p><strong>Original Appointment Request:</strong></p>
                <p><strong>Date:</strong> {{ $appointment->appointment_date->format('F d, Y') }}</p>
                <p><strong>Time:</strong> {{ $appointment->appointment_time ? date('g:i A', strtotime($appointment->appointment_time)) : 'To be determined' }}</p>
                @if($appointment->concern)
                <p><strong>Concern:</strong> {{ $appointment->concern }}</p>
                @endif
            </div>
            
            @if($appointment->action_reason)
            <div class="reason">
                <p><strong>Reason for Declining:</strong></p>
                <p>{{ $appointment->action_reason }}</p>
            </div>
            @endif
            
            <p>We apologize for any inconvenience this may cause. If you have any questions or would like to schedule a new appointment, please feel free to contact us.</p>
            
            <p>Thank you for your understanding.</p>
            
            <p>Best regards,<br>
            <strong>OSA Balubal Team</strong></p>
        </div>
    </div>
</body>
</html>
