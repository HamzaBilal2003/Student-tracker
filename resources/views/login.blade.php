<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.brand_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name='csrf-token' content="{{csrf_token()}}">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .login-title {
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #343a40;
        }
        .form-control:focus {
            border-color: #6c757d;
            box-shadow: none;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .form-footer {
            text-align: center;
            margin-top: 1rem;
        }
        .form-footer a {
            color: #6c757d;
            text-decoration: none;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2 class="login-title">{{ config('app.brand_name') }}</h2>
    <h5 class="text-center text-muted mb-4">Login to Your Account</h5>
    <form id="loginForm">
        @if (session('error'))
            <div class="alert alert-danger d-block" id="error-message">
                {{session('error')}}
            </div>
        @endif
        <div class="alert alert-success" id="success-message"></div>
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $('#loginForm').on('submit', function (e) {
            e.preventDefault();

            $('#error-message').hide();
            $('#success-message').hide();

            // Get form data
            let email = $('#email').val();
            let password = $('#password').val();

            $.ajax({
                url: '{{route('auth.login')}}',
                type: 'POST',
                data: {
                    email: email,
                    password: password,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        $('#success-message').text(response.message).show();
                        window.location.href = '{{route('dashboard.index')}}';
                    } else {
                        $('#error-message').text(response.message).show();
                    }
                },
                error: function (xhr, status, error) {
                    $('#error-message').text('An error occurred. Please try again.').show();
                }
            });
        });
    });
</script>
</body>
</html>
