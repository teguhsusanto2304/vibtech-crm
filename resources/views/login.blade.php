<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vibtech Genesis</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/favicon/fav.svg') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            background-color: #0C3B5D;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
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
            background: url('{{ asset('assets/img/a01388c1-da64-4861-951c-292fbd7fb3f01x.png') }}') no-repeat center center;
            background-size: cover;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .login-container {
                width: 100%;
                padding: 2rem 1rem;
            }
            .image-container {
                display: none; /* Hide the image on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row align-items-center vh-100">
            <!-- Login Section -->
            <div class="col-lg-5 col-md-6 mx-auto login-container text-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Vibtech Genesis" width="150">
                <p><em>No Problem! Only Solutions</em></p>

                <form method="POST" action="{{ route('v1.login') }}">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <input type="text" name="email" placeholder="Staff Email">
                    <input type="password" name="password" placeholder="Password">
                    <button type="submit">Login</button>
                </form>
                <p class="mt-3"><a href="{{ route('v1.password.forgot') }}" class="text-white">Reset Password</a></p>
                <p class="mt-5"><a href="https://www.vibtech-genesis.com/" target="_blank" class="text-white">www.vibtech-genesis.com</a></p>
            </div>

            <!-- Image Section (Hidden on small screens) -->
            <div class="col-lg-7 col-md-6 image-container d-none d-md-block vh-100"></div>
        </div>
    </div>
</body>
</html>
