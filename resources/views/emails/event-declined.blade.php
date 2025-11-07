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
            background-color: #dc3545;
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
            border-left: 4px solid #dc3545;
        }
        .reason-box {
            background-color: #fff3cd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Event Declined</h2>
        </div>
        <div class="content">
            <p>Dear {{ $event->organization->name ?? 'Organization' }} Representative,</p>
            
            <p>We regret to inform you that your event has been <strong>declined</strong>.</p>
            
            <div class="details">
                <p><strong>Event Details:</strong></p>
                <p><strong>Event Name:</strong> {{ $event->name }}</p>
                <p><strong>Description:</strong> {{ $event->description ?? 'N/A' }}</p>
                @if($event->start_time)
                <p><strong>Start Date/Time:</strong> {{ \Carbon\Carbon::parse($event->start_time)->format('F d, Y h:i A') }}</p>
                @endif
                @if($event->end_time)
                <p><strong>End Date/Time:</strong> {{ \Carbon\Carbon::parse($event->end_time)->format('F d, Y h:i A') }}</p>
                @endif
                @if($event->location)
                <p><strong>Location:</strong> {{ $event->location }}</p>
                @endif
            </div>

            @if($event->decline_reason)
            <div class="reason-box">
                <p><strong>Reason for Decline:</strong></p>
                <p>{{ $event->decline_reason }}</p>
            </div>
            @endif
            
            <p>This event is now considered closed and cannot be edited or updated. If you have any questions or concerns, please contact the OSA office.</p>
            
            <p>Thank you for your understanding.</p>
            
            <p>Best regards,<br>
            <strong>OSA Balubal Team</strong></p>
        </div>
    </div>
</body>
</html>

