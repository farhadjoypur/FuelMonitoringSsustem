<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fuel Monitoring System</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #01579b 0%, #003366 100%);
            position: relative;
            overflow: hidden;
        }

        .login-wrapper::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.03);
            transform: skewY(-10deg);
            top: -50%;
            pointer-events: none;
        }

        .left-content {
            color: white;
            z-index: 1;
            padding-left: 10%;
        }

        .bd-logo {
            width: 60px;
            margin-bottom: 15px;
        }

        .welcome-text {
            font-size: 1.1rem;
            margin-bottom: 0;
            font-weight: 500;
        }

        .system-title {
            font-size: 2.2rem;
            /* টাইটেল কিছুটা ছোট করা হয়েছে */
            font-weight: 700;
            line-height: 1.2;
        }

        .login-card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
        }

        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            width: 100%;
            max-width: 360px;
            /* কার্ড ছোট করা হয়েছে */
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .avatar-circle {
            width: 60px;
            height: 60px;
            background: #006699;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin: 0 auto 15px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 2px;
        }

        .card-subtitle {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #444;
            margin-bottom: 4px;
        }

        .input-group-text {
            background: white;
            border-right: none;
            color: #999;
            padding: 0.5rem 0.7rem;
        }

        .form-control {
            border-left: none;
            font-size: 0.9rem;
            padding: 0.5rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        .btn-login {
            background: #006699;
            border: none;
            width: 100%;
            padding: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #004c73;
        }

        @media (max-width: 767px) {
            .left-content {
                padding-left: 0;
                text-align: center;
                margin-bottom: 30px;
            }

            .login-wrapper {
                flex-direction: column;
                justify-content: center;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 left-content">
                    <img src="{{ asset('backend/assets/images/logo.png') }}" alt="BD Logo" class="bd-logo">
                    <p class="welcome-text">Welcome To</p>
                    <h1 class="system-title">Fuel Monitoring <br> System</h1>
                </div>

                <div class="col-md-6 login-card-container">
                    <div class="login-card">
                        <div class="avatar-circle">FM</div>
                        <h2 class="card-title">Fuel Monitoring System</h2>
                        <p class="card-subtitle my-3">Login Here</p>

                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            <div class="text-start mb-3">
                                <label class="form-label">Email or Phone</label>
                                <div class="input-group">
                                    <span
                                        class="input-group-text @error('identifier') border-danger text-danger @enderror">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" name="identifier"
                                        class="form-control @error('identifier') is-invalid @enderror"
                                        placeholder="Enter email or phone" value="{{ old('identifier') }}">
                                </div>
                                @error('identifier')
                                    <div class="text-danger mt-1" style="font-size: 0.8rem;">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>

                            <div class="text-start mb-4">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span
                                        class="input-group-text @error('password') border-danger text-danger @enderror">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Enter password">
                                </div>
                                @error('password')
                                    <div class="text-danger mt-1" style="font-size: 0.8rem;">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-login">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "toastClass": 'toast toast-full-opacity'
            };
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if (session('info'))
                toastr.info("{{ session('info') }}");
            @endif

            @if (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif
        });
    </script>
</body>

</html>
