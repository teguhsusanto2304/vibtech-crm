<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Vibtech Genesis</title>
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
            background: url('{{ asset($imagePath) }}') no-repeat center center;
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

                <form method="POST" action="{{ route('v1.password.reset-link') }}">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    @if (session('status'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <input type="text" name="email" placeholder="Your Email">
                    <button type="submit">Password Reset</button>
                </form>
                <p class="mt-3"><a href="{{ route('v1.login') }}" class="text-white">Back to Login</a></p>
                <p class="mt-5">www.vibtech-genesis.com</p>
            </div>

            <!-- Image Section (Hidden on small screens) -->
            <div class="col-lg-7 col-md-6 image-container d-none d-md-block vh-100"></div>
        </div>
    </div>
</body>
</html>
