<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(180deg, #eef2ff 0%, #ffffff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
        }

        .login-card {
            border-radius: 1.5rem;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.16);
            overflow: hidden;
        }

        .login-card .card-header {
            background: linear-gradient(135deg, #4f46e5 0%, #22c55e 100%);
            border: none;
            color: #ffffff;
            text-align: center;
            padding: 1.75rem;
        }

        .login-card .card-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.24);
            box-shadow: none;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.16);
        }

        .btn-login {
            border-radius: 1rem;
            padding: 0.9rem 1.2rem;
            font-weight: 700;
        }

        .login-subtitle {
            color: #64748b;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card">
                <div class="card-header">
                    <h4>Welcome Back</h4>
                    <p class="login-subtitle mb-0">Sign in to access your task dashboard.</p>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger rounded-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-login w-100">
                            Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>