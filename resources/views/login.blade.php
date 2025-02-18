<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vibtech Genesis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            background-color: #0C3B5D;
        }
        .login-container {
            width: 40%;
            background: #0C3B5D;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-container input {
            background: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 20px;
            margin-bottom: 1rem;
        }
        .login-container button {
            width: 100%;
            border-radius: 20px;
            padding: 10px;
            background: #F59E0B;
            border: none;
            color: white;
            font-weight: bold;
        }
        .image-container {
            width: 60%;
            background: url('../assets/img/banner.png') no-repeat center center;
            background-size: cover;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../assets/img/logo.png" alt="Vibtech Genesis" width="150">
        <p><em>No Problem! Only Solutions</em></p>

        <form method="POST" action="{{ route('v1.login') }}">
            @csrf
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ $errors->first() }}
            </div>
        @endif
        <input type="text" name="email" placeholder="Staff ID">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
        </form>
        <p class="mt-3"><a href="#" class="text-white">Reset Password</a></p>
        <p class="mt-5">www.vibtech-genesis.com</p>
    </div>
    <div class="image-container"></div>
</body>
</html>
