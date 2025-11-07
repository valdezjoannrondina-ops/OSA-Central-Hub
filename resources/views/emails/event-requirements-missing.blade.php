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
            background-color: #ffc107;
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
            border-left: 4px solid #ffc107;
        }
        .requirements-box {
            background-color: #fff3cd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Missing Requirements for Event</h2>
        </div>
        <div class="content">
            <p>Dear {{ $event->organization->name ?? 'Organization' }} Representative,</p>
            
            <p>We are writing to inform you that your event is missing some required documents or information.</p>
            
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
            </div>

            @if($missingRequirements && $missingRequirements->isNotEmpty())
            <div class="requirements-box">
                <p><strong>Missing Requirements:</strong></p>
                <ul>
                    @foreach($missingRequirements as $requirement)
                    <li>{{ $requirement }}</li>
                    @endforeach
                </ul>
            </div>
            @else
            <div class="requirements-box">
                <p><strong>Please note:</strong> Some required documents or information are missing for this event. Please review the event requirements and submit all necessary documents.</p>
            </div>
            @endif
            
            <p>Please submit the missing requirements as soon as possible to ensure your event can proceed as scheduled. Failure to submit required documents may result in the event being declined.</p>
            
            <p>If you have any questions or need assistance, please contact the OSA office.</p>
            
            <p>Thank you for your cooperation.</p>
            
            <p>Best regards,<br>
            <strong>OSA Balubal Team</strong></p>
        </div>
    </div>
</body>
</html>

