<!DOCTYPE html>
<html>
<head>
    <title>A New Memo</title>
</head>

<body>
    <h2>Dear <strong>{{ $post['employee_name'] }}</strong>,</h2><br>
    <p>{{ $post['creator_name'] }} has uploaded a new staff handbook.
Please <a href="{{ route('v1.login') }}">login</a> to the staff portal to read the handbook.<br>
If you have any questions, feel free to reach out to the management. 
</p>
    <br>
    <p>Thank you</p>
</body>
</html>
