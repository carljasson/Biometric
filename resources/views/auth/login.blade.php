<!DOCTYPE html>
<html>
<head>
    <title>Login - Biometric Medical Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: url('/images/background.png') no-repeat center center fixed;
            background-size: cover;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s;
            z-index: 10;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            padding: 30px;
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card h4 {
            font-weight: bold;
            color: #333;
        }

        .form-label {
            font-weight: 500;
        }

        .alert {
            font-size: 0.9rem;
        }

        .register-link {
            font-size: 0.95rem;
        }

    </style>
</head>
<body>
    <a href="javascript:void(0)" onclick="window.history.back(); return false;" class="back-button">← Back</a>

    <div class="login-card">
        <div class="text-center mb-4">
            <h4>🔒 Login to Access</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required />
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="mt-3 text-center register-link">
            Don't have an account? <a href="{{ url('/register/step1') }}">Register here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
