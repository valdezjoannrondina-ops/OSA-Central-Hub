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
            background-color: #00D9A5;
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
            border-left: 4px solid #00D9A5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Appointment Approved</h2>
        </div>
        <div class="content">
            <p>Dear {{ $appointment->full_name }},</p>
            
            <p>We are pleased to inform you that your appointment has been <strong>approved</strong>.</p>
            
            <div class="details">
                <p><strong>Appointment Details:</strong></p>
                <p><strong>Date:</strong> {{ $appointment->appointment_date->format('F d, Y') }}</p>
                <p><strong>Time:</strong> {{ $appointment->appointment_time ? date('g:i A', strtotime($appointment->appointment_time)) : 'To be determined' }}</p>
                @if($appointment->concern)
                <p><strong>Concern:</strong> {{ $appointment->concern }}</p>
                @endif
            </div>
            
            <p><strong>OSA Balubal is looking forward to meeting you personally.</strong></p>
            
            <p>Please arrive on time for your appointment. If you have any questions or need to reschedule, please contact us as soon as possible.</p>
            
            <p>Thank you for using OSA Central Hub.</p>
            
            <p>Best regards,<br>
            <strong>OSA Balubal Team</strong></p>
        </div>
    </div>
</body>
</html>
