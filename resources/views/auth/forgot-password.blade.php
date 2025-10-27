<!DOCTYPE html>
<html lang="en">

<head>
    <title>Forgot Password - Biometric Medical Access</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    @php $cspNonce = bin2hex(random_bytes(16)); @endphp

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    <style nonce="{{ $cspNonce }}">
        body {
            background: url('{{ asset('images/background.png') }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            padding: 30px;
            width: 100%;
            max-width: 450px;
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px);} to { opacity: 1; transform: translateY(0);} }

        .form-label { font-weight: 500; }
        .alert { font-size: 0.9rem; }
        .extra-links { font-size: 0.95rem; }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h4>🔑 Reset Password</h4>
            <p class="text-dark small">Enter your email to receive reset instructions.</p>
        </div>

        {{-- ✅ Success Notification --}}
        @if (session('status'))
            <div class="alert alert-success text-center">{{ session('status') }}</div>
        @endif

        {{-- ❌ Validation Errors --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ e($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ✅ Reset Form --}}
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       required autofocus autocomplete="email">
            </div>

            {{-- ✅ Cloudflare Turnstile --}}
            <div class="cf-turnstile mb-3" data-sitekey="{{ config('services.turnstile.sitekey') }}"></div>

            <button type="submit" class="btn btn-primary w-100">
                Send Password Reset Link
            </button>
        </form>

        <div class="text-center mt-3 extra-links">
            <a href="{{ url('/login') }}">Back to Login</a>
        </div>
    </div>

    {{-- ✅ Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            nonce="{{ $cspNonce }}"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js"
            async defer></script>
</body>

</html>
