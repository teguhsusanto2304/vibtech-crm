<!DOCTYPE html>
<html>
<head>
    <title>Report</title>
</head>

<body>
    <h2>Hallo Management</h2>
    <p>There is a new whistleblowing report. Please review report below.</p>
    <p>{!! $data['description'] !!}</p>
    <p>Sent By: {{ $data['createdBy'] }}</p>
    <p>Please contact staff directly to address concern.</p>
    <p>Thank you</p>
    <br>
    <p><small>This email is auto-generated by Vibtech Genesis Staff Portal System. Please do not reply to this email. </small><p>
</body>
</html>
