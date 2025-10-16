<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    @php $cspNonce = bin2hex(random_bytes(16)); @endphp

    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        script-src 'self' https://cdn.jsdelivr.net https://challenges.cloudflare.com 'nonce-{{ $cspNonce }}';
        style-src 'self' https://cdn.jsdelivr.net 'nonce-{{ $cspNonce }}';
        img-src 'self' data:;
    ">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          referrerpolicy="no-referrer">

    <style nonce="{{ $cspNonce }}">
        body {
            background: url('{{ asset('images/background.png') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .card-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
        }
    </style>
</head>
<body>

<div class="card-box">
    <h4 class="text-center">🔁 Forgot Password</h4>

    @if (session('status'))
        <div class="alert alert-success text-center">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" autocomplete="off">
        @csrf
        <div class="mb-3">
            <label class="form-label">Enter your Email</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>

        {{-- 🌐 Cloudflare Turnstile --}}
        <div class="cf-turnstile mb-3" data-sitekey="{{ config('services.turnstile.sitekey') }}"></div>

        <button class="btn btn-primary w-100">Send Reset Link</button>
    </form>

    <div class="mt-3 text-center">
        <a href="{{ url('/login') }}">← Back to Login</a>
    </div>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</body>
</html>
