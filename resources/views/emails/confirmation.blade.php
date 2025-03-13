<!DOCTYPE html>
<html>
<head>
    <title>Job Requisition Form</title>
</head>

<body>
    <h2>Dear <strong>{{ $booking['personel'] }}</strong>,</h2>
    <p>{{ $booking['originator'] }} has a Job Request and has invited you to collaborate on the job</td><td>:</td><td></p>
    <p>Job Details</td><td>:</td><td></p>
    <table with="100%">
    <tr>
        <td width="20%">Job Record ID</td>
        <td width="1%">:</td>
        <td width="79%">{{ $booking['job_record_id'] }}</td>
    </tr>
    <tr>
        <td>Type of Job</td>
        <td>:</td>
        <td> {{ $booking['type_job'] }}</td>
    </tr>
    <tr>
        <td>Scope of Work</td>
        <td>:</td>
        <td> <p style="text-align: justify;">{{ $booking['scope_of_work'] }}</p></td>
    </tr>
    <tr>
        <td>Start Date</td>
        <td>:</td>
        <td> {{ $booking['start_at'] }}</td>
    </tr>
    <tr>
        <td>End Date</td>
        <td>:</td>
        <td> {{ $booking['end_at'] }}</td>
    </tr>
    <tr>
        <td>Vehicle Required</td>
        <td>:</td>
        <td> {{ $booking['is_vehicle_require'] }}</td>
    </tr>
    </table>
    <p>Please login to the company portal to view and respond to the request.</p>
    <p><a href="{{ $booking['url'] }}">Link to Job Requisition</a></p>
    <p>If you have any questions, feel free to reach out to {{ $booking['originator'] }}.</p>
    <br>
    <p>Thank you</p>
</body>
</html>
