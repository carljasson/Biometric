<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Biometric Medical Access</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- 🔐 Generate CSP nonce --}}
    @php $cspNonce = bin2hex(random_bytes(16)); @endphp

    {{-- 🔐 Security headers --}}
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        base-uri 'self';
        form-action 'self';
        object-src 'none';
        frame-ancestors 'none';
        upgrade-insecure-requests;
        script-src 'self' https://cdn.jsdelivr.net 'nonce-{{ $cspNonce }}';
        style-src  'self' https://cdn.jsdelivr.net 'nonce-{{ $cspNonce }}';
        img-src 'self' data:;
        font-src 'self' https://cdn.jsdelivr.net;
        connect-src 'self';
    ">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="Referrer-Policy" content="no-referrer">
    <meta http-equiv="Permissions-Policy" content="geolocation=(), microphone=(), camera=()">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

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
    {{-- 🔙 Back Button --}}
    <a href="javascript:void(0)" onclick="window.history.back(); return false;"
       class="back-button" rel="noopener noreferrer">← Back</a>

    <div class="login-card">
        <div class="text-center mb-4">
            <h4>🔒 Login to Access</h4>
        </div>

        {{-- ✅ Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success text-center" role="status" aria-live="polite">
                {{ session('success') }}
            </div>
        @endif

        {{-- ❌ Error messages --}}
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ e($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🔐 Login form with CSRF --}}
        <form method="POST" action="{{ url('/login') }}" autocomplete="off">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email"
                       name="email"
                       class="form-control"
                       required
                       autocomplete="username"
                       inputmode="email">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password"
                       name="password"
                       class="form-control"
                       required
                       autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="mt-3 text-center register-link">
            Don't have an account?
            <a href="{{ url('/register/step1') }}" rel="noopener noreferrer">Register here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            nonce="{{ $cspNonce }}"
            crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>

    {{-- 🔒 Frame busting --}}
    <script nonce="{{ $cspNonce }}">
        if (window.top !== window.self) {
            window.top.location = window.self.location;
        }
    </script>

    {{-- 🚨 SweetAlert2 Lockout --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" nonce="{{ $cspNonce }}"></script>

    @if(session('lockout'))
    <script nonce="{{ $cspNonce }}">
    document.addEventListener("DOMContentLoaded", function () {
        let seconds = {{ session('lockout') }};
        let form = document.querySelector(".login-card form");

        if (form) {
            form.style.display = "none"; // hide login form
        }

        Swal.fire({
            icon: 'error',
            title: 'Too Many Attempts',
            html: 'You have failed 3 login attempts.<br>Please try again in <b><span id="countdown"></span></b> seconds.',
            allowOutsideClick: false,
            showConfirmButton: false
        });

        const countdownEl = document.getElementById("countdown");
        countdownEl.innerText = seconds;

        const timer = setInterval(() => {
            seconds--;
            countdownEl.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.reload(); // reload page when cooldown ends
            }
        }, 1000);
    });
    </script>
    @endif
</body>
</html>