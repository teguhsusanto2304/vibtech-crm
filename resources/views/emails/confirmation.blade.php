<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h2>Hi <strong>{{ $booking['name'] }}</strong>,</h2>
    <p>Your booking for a vehicle has been confirmed.</p>
    <p><strong>Start Date:</strong> {{ $booking['start_at'] }}</p>
    <p><strong>End Date:</strong> {{ $booking['end_at'] }}</p>
    <p><strong>Purpose:</strong> {{ $booking['purposes'] }}</p>
    <p>Thank you for using our service!</p>
</body>
</html>
