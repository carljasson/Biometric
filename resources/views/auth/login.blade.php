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
        script-src 'self' https://cdn.jsdelivr.net https://challenges.cloudflare.com 'nonce-{{ $cspNonce }}';
        style-src  'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'nonce-{{ $cspNonce }}';
        img-src 'self' data: https://cdnjs.cloudflare.com https://cdn.jsdelivr.net;
        font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net data:;
        connect-src 'self';
        frame-src https://challenges.cloudflare.com;
    ">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="Referrer-Policy" content="no-referrer">
    <meta http-equiv="Permissions-Policy" content="geolocation=(), microphone=(), camera=()">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    {{-- ✅ Font Awesome added --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
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

        /* ✅ Top-right icons */
        .top-right-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 12px;
            z-index: 999;
        }
        .top-right-icons a,
        .top-right-icons .dropdown-toggle {
            color: white;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 10px;
            border-radius: 50%;
            font-size: 1.3rem;
            transition: 0.3s;
            text-decoration: none;
        }
        .top-right-icons a:hover,
        .top-right-icons .dropdown-toggle:hover {
            background-color: rgba(255,255,255,0.25);
        }

        .dropdown-menu-dark { background-color: #343a40; }

        .login-card {
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(8px);
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

        .login-card h4 { font-weight: bold; color: #333; }

        .form-label { font-weight: 500; }

        .alert { font-size: 0.9rem; }

        .extra-links { font-size: 0.95rem; }
    </style>
</head>

<body>

    {{-- ✅ Top-right functional icons --}}
    <div class="top-right-icons">

        <a href="/" title="Home"><i class="fas fa-home"></i></a>

        {{-- ✅ Login Dropdown --}}
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" id="loginDropdown"
               data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-sign-in-alt"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="loginDropdown">
                <li><a class="dropdown-item" href="{{ route('login') }}">Login as User</a></li>
                <li><a class="dropdown-item" href="{{ route('responder.login') }}">Login as Responder</a></li>
            </ul>
        </div>

        {{-- ✅ Register Modal Trigger --}}
        <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" title="Signup">
            <i class="fas fa-user-plus"></i>
        </a>

        {{-- ✅ Tips Modal Trigger --}}
        <a href="#" data-bs-toggle="modal" data-bs-target="#tipsModal" title="Tips">
            <i class="fas fa-lightbulb"></i>
        </a>
    </div>

    <div class="login-card">
        <div class="text-center mb-4">
            <h4>🔒 Login to Access</h4>
        </div>

        {{-- ✅ Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        {{-- ❌ Error messages --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ e($error) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🔐 Login form --}}
        <form method="POST" action="{{ url('/login') }}" autocomplete="off">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required inputmode="email" autocomplete="username">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>

            {{-- 🌐 Cloudflare Turnstile --}}
            <div class="cf-turnstile mb-3" data-sitekey="{{ config('services.turnstile.sitekey') }}"></div>
            @error('captcha')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="mt-3 text-center extra-links">
<a href="{{ route('password.request') }}">Forgot password?</a>

        </div>
    </div>


    {{-- ✅ Popups (same as Home) --}}
    <div class="modal fade" id="registerModal">
        <div class="modal-dialog"><div class="modal-content p-3">
            🚫 Registration is only available **at the MDRRMO office**
        </div></div>
    </div>

    <div class="modal fade" id="tipsModal">
        <div class="modal-dialog"><div class="modal-content p-3">
            🚑 This system alerts the nearest responders & provides victim info!
        </div></div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            nonce="{{ $cspNonce }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
            nonce="{{ $cspNonce }}"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js"
            async defer></script>

    {{-- 🚫 Prevent iframe embedding --}}
    <script nonce="{{ $cspNonce }}">
        if (window.top !== window.self) window.top.location = window.self.location;
    </script>

    @if(session('lockout'))
    <script nonce="{{ $cspNonce }}">
        document.addEventListener("DOMContentLoaded", () => {
            let seconds = {{ session('lockout') }};
            let form = document.querySelector(".login-card form");
            if (form) form.style.display = "none";

            Swal.fire({
                icon: 'error',
                title: 'Too Many Attempts',
                html: `Wait <b>${seconds}</b> seconds.`,
                allowOutsideClick: false,
                showConfirmButton: false
            });

            const timer = setInterval(() => {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.reload();
                }
            }, 1000);
        });
    </script>
    @endif

</body>
</html>
