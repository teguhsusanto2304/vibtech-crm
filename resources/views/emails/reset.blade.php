<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>

<body>
    <h2>Dear <strong>{{ $user['name'] }}</strong>,</h2>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <a href="{{ $user['resetLink'] }}">Go to Reset Password Page</a>
    <p>This password reset link will expire in 60 minutes.</p>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>Regards,</p>
    <br>
    <p>Thank you</p>
</body>
</html>
