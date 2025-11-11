<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Biometric Medical Access</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

    @php $cspNonce = bin2hex(random_bytes(16)); @endphp

    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        base-uri 'self';
        form-action 'self';
        object-src 'none';
        frame-ancestors 'none';
        upgrade-insecure-requests;
        script-src 'self' https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com https://recaptcha.net 'nonce-{{ $cspNonce }}';
        style-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com 'unsafe-inline' 'nonce-{{ $cspNonce }}';
        img-src 'self' data: https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://www.google.com https://www.gstatic.com https://recaptcha.net;
        font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net data:;
        frame-src https://www.google.com https://www.gstatic.com https://recaptcha.net;
        connect-src 'self' https://www.google.com https://www.gstatic.com https://recaptcha.net;
    ">

    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="Referrer-Policy" content="no-referrer">
    <meta http-equiv="Permissions-Policy" content="geolocation=(), microphone=(), camera=()">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <style nonce="{{ $cspNonce }}">
        body {
            background: url('{{ asset('images/background.png') }}') no-repeat center center fixed;
            background-size: cover;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        html, body {
            touch-action: manipulation;
        }
        input, select, textarea {
            font-size: 16px;
        }

        .login-container {
            display: flex;
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
            max-width: 900px;
            width: 90%;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.6);
            animation: fadeIn 0.6s ease-in-out;
            position: relative;
        }

        .login-left, .login-right {
            flex: 1;
            padding: 40px;
        }

        .login-left {
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 2px solid rgba(255,255,255,0.6);
            background-color: rgba(0,0,0,0.1);
        }

        .login-left img {
            max-width: 200px;
            height: auto;
        }

        .login-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        /* X button */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px;
            height: 32px;
            background-color: rgba(255,255,255,0.8);
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            font-weight: bold;
            font-size: 20px;
            color: #333;
            text-decoration: none;
            transition: 0.3s;
        }
        .close-btn:hover {
            background-color: rgba(255,0,0,0.8);
            color: #fff;
        }

        .login-right h4 {
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }

        .form-label { font-weight: 500; }
        .alert { font-size: 0.9rem; }
        .extra-links { font-size: 0.95rem; margin-top: 15px; }

        /* Dimming effect for disabled form */
        .disabled-form {
            opacity: 0.5;
            pointer-events: none;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="login-container">

    <!-- Left side: Logo -->
    <div class="login-left">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>

    <!-- Right side: Login form -->
    <div class="login-right">
        <!-- X button -->
        <a href="/" class="close-btn">×</a>

        <h4 class="text-center">🔒 Login to Access</h4>

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ e($error) }}</li>@endforeach</ul>
            </div>
        @endif

        <form id="loginForm" method="POST" action="{{ url('/login') }}" autocomplete="off">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required inputmode="email" autocomplete="username" value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>

            {{-- Google reCAPTCHA v3 --}}
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            @error('g-recaptcha-response')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center extra-links">
            <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>
    </div>

</div>

<div class="modal fade" id="registerModal"><div class="modal-dialog"><div class="modal-content p-3">🚫 Registration is only available **at the MDRRMO office**</div></div></div>
<div class="modal fade" id="tipsModal"><div class="modal-dialog"><div class="modal-content p-3">🚑 This system alerts the nearest responders & provides victim info!</div></div></div>

<!-- PIN Modal -->
<div class="modal fade" id="pinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-4">
            <h5 class="mb-3">Enter the PIN sent to your email</h5>

            @if($errors->has('pin'))
                <div class="alert alert-danger">{{ $errors->first('pin') }}</div>
            @endif

            <form id="pinForm" method="POST" action="{{ url('/login/pin') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="pin" class="form-control" maxlength="6" required placeholder="6-digit PIN">
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify PIN</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" nonce="{{ $cspNonce }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" nonce="{{ $cspNonce }}"></script>
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.sitekey') }}" nonce="{{ $cspNonce }}"></script>

<script nonce="{{ $cspNonce }}">
document.getElementById('loginForm').addEventListener('submit', function (event) {
    event.preventDefault();
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ config('services.recaptcha.sitekey') }}', {action: 'login'}).then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
            event.target.submit();
        });
    });
});
</script>

<!-- Show PIN modal if session exists -->
<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', () => {
    @if(session('showPinModal'))
        setTimeout(() => {
            var pinModal = new bootstrap.Modal(document.getElementById('pinModal'));
            pinModal.show();
        }, 200);
    @endif
});
</script>

<script nonce="{{ $cspNonce }}">
if (window.top !== window.self) window.top.location = window.self.location;
</script>

{{-- Lockout handler --}}
@if(session('lockout'))
<script nonce="{{ $cspNonce }}">
document.addEventListener("DOMContentLoaded", () => {
    let seconds = {{ session('lockout') }};
    let form = document.querySelector(".login-right form");

    if (form) {
        form.querySelectorAll('input, button').forEach(el => el.disabled = true);
        form.classList.add('disabled-form');
    }

    Swal.fire({
        icon: 'error',
        title: 'Too Many Attempts',
        html: `You have been locked out.<br>Wait <b>${seconds}</b> seconds.`,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            const interval = setInterval(() => {
                seconds--;
                Swal.update({
                    html: `You have been locked out.<br>Wait <b>${seconds}</b> seconds.`
                });
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.reload();
                }
            }, 1000);
        }
    });
});
</script>
@endif

</body>
</html>
