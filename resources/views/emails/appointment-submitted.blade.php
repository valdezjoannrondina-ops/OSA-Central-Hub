<html>
<body>
    <h2>Your Appointment Request Has Been Submitted</h2>
    <p>Dear {{ $appointment->full_name }},</p>
    <p>Your appointment request has been received with the following details:</p>
    <ul>
        <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}</li>
        <li><strong>Time:</strong> {{ date('g:i A', strtotime($appointment->appointment_time)) }}</li>
        <li><strong>Contact Number:</strong> {{ $appointment->contact_number }}</li>
        <li><strong>Email:</strong> {{ $appointment->email }}</li>
        <li><strong>Concern:</strong> {{ $appointment->concern }}</li>
    </ul>
    <p>OSA will respond to your request as soon as possible. Thank you for using OSA Central Hub.</p>
</body>
</html>
