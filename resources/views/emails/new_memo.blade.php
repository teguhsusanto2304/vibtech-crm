<!DOCTYPE html>
<html>
<head>
    <title>A New Memo</title>
</head>

<body>
    <h2>Dear <strong>{{ $post['employee_name'] }}</strong>,</h2><br>
    <p>{{ $post['creator_name'] }} 
        @if($post['method']=='insert')
        has issued a new management memo.
        @else
        has updated an existing management memo.
        @endif
Please <a href="{{ route('v1.login') }}">login</a> to the staff portal to read and acknowledge the memo.<br>
If you have any questions, feel free to reach out to the management. 
</p>
    <br>
    <p>Thank you</p>
</body>
</html>
