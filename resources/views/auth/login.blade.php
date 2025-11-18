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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <style nonce="{{ $cspNonce }}">
        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #3498db, #8e44ad);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-wrapper {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.15);
            width: 90%;
            max-width: 450px;
            display: flex;
            overflow: hidden;
            padding: 40px;
            flex-direction: column;
            position: relative;
            animation: fadeIn 0.6s ease-in-out;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .close-btn:hover { color: #e74c3c; }

        .user-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #fff;
            font-weight: bold;
            margin: 0 auto 20px auto;
        }

        .login-wrapper h4 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 25px;
            color: #333;
        }

        .form-label { font-weight: 600; }

        .btn-primary {
            background: #3498db;
            border: none;
            transition: background 0.3s ease;
        }
        .btn-primary:hover { background: #2980b9; }

        .extra-links { font-size: 0.95rem; margin-top: 15px; text-align: center; }

        .disabled-form { opacity: 0.5; pointer-events: none; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 768px) {
            .login-wrapper { padding: 30px 20px; width: 95%; max-width: 400px; }
        }

        * { touch-action: manipulation; user-select: none; }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- Close button -->
    <span class="close-btn" onclick="window.location='{{ route('welcome') }}'">&times;</span>

    <!-- User Icon -->
    <div class="user-icon">
        <i class="fa-solid fa-user"></i> U
    </div>

    <h4>🔒 Login to Access</h4>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ e($error) }}</li>
                @endforeach
            </ul>
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

        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        @error('g-recaptcha-response')
            <div class="alert alert-danger mt-2">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <div class="extra-links">
        <a href="{{ route('password.request') }}">Forgot password?</a>
    </div>

</div>

<!-- Modals -->
<div class="modal fade" id="registerModal"><div class="modal-dialog"><div class="modal-content p-3">🚫 Registration is only available **at the MDRRMO office**</div></div></div>
<div class="modal fade" id="tipsModal"><div class="modal-dialog"><div class="modal-content p-3">🚑 This system alerts the nearest responders & provides victim info!</div></div></div>
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

@if(session('lockout'))
<script nonce="{{ $cspNonce }}">
document.addEventListener("DOMContentLoaded", () => {
    let seconds = {{ session('lockout') }};
    let form = document.querySelector(".login-wrapper form");
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
                Swal.update({ html: `You have been locked out.<br>Wait <b>${seconds}</b> seconds.` });
                if (seconds <= 0) { clearInterval(interval); window.location.reload(); }
            }, 1000);
        }
    });
});
</script>
@endif

</body>
</html>
