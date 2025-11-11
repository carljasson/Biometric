<!DOCTYPE html>
<html lang="en">

<head>
    <title>Biometric Medical Access</title>

    {{-- 🔐 Generate a nonce for CSP-safe inline <style> and <script> --}}
    @php $cspNonce = bin2hex(random_bytes(16)); @endphp

    {{-- 🔐 Security headers as meta (best set in real HTTP headers too) --}}
<meta http-equiv="Content-Security-Policy" content="
    default-src 'self';
    base-uri 'self';
    form-action 'self';
    object-src 'none';
    frame-ancestors 'none';
    upgrade-insecure-requests;
    script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'nonce-{{ $cspNonce }}';
    style-src  'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'unsafe-inline' 'nonce-{{ $cspNonce }}';
    img-src 'self' data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;
    font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com data:;
    connect-src 'self';
">

    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="no-referrer">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- ✅ Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    {{-- ✅ Font Awesome (still from CDN) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          rel="stylesheet"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    {{-- ✅ Local Bootstrap Icons (fix for CSP-blocked icons) --}}
    <link rel="stylesheet" href="{{ asset('bootstrap-icons/bootstrap-icons.css') }}">

    {{-- ✅ Fullscreen scalable background --}}
    <style nonce="{{ $cspNonce }}">
        html, body {
            height: 100%;
            margin: 0;
        }

        /* Full-screen background that always covers */
        .bg-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* behind everything */
            background: url('{{ asset('images/background.png') }}') center center / cover no-repeat;
        }

        body {
            color: white;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
        }

        .top-left-logo { position: absolute; top: 5px; left: 20px; }
        .top-left-logo img { max-width: 180px; width: 100%; height: auto; }

        .top-right-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .top-right-icons a,
        .top-right-icons .dropdown-toggle {
            color: white;
            font-size: 1.5rem;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .top-right-icons a:hover,
        .top-right-icons .dropdown-toggle:hover {
            background-color: rgba(255,255,255,0.2);
        }

        .dropdown-menu-dark { background-color: #343a40; }
        .alert { margin: 1rem auto; width: 90%; max-width: 500px; }

        .modal-backdrop { opacity: 0.8; }
        .modal-content {
            background-color: rgba(0, 0, 0, 0.9);
            color: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
        }
        .modal-header { border-bottom: 1px solid #444; }
        .modal-body { font-size: 1.1rem; line-height: 1.6; }
        .modal-footer button { background-color: #007bff; color: white; }
        .modal-footer button:hover { background-color: #0056b3; }
        .btn-close {
            background-color: #ff4747;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
        }
        .btn-close:hover { background-color: #ff0000; }
    </style>
</head>

<body>
    {{-- ✅ Fullscreen background --}}
    <div class="bg-wrapper"></div>

    {{-- Top Left Logo --}}
    <div class="top-left-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Biometric Medical Access Logo" loading="lazy" decoding="async">
    </div>

    {{-- Top Right Icons --}}
    <div class="top-right-icons">
        <a href="/" title="Dashboard" rel="noopener noreferrer" referrerpolicy="no-referrer">
            <i class="bi bi-house-door-fill"></i>
        </a>

        {{-- Login Dropdown --}}
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" id="loginDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Login">
                <i class="bi bi-box-arrow-in-right"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="loginDropdown">
                <li><a class="dropdown-item" href="{{ route('login') }}">Login as User</a></li>
                <li><a class="dropdown-item" href="{{ route('responder.login') }}">Login as Responder</a></li>
            </ul>
        </div>

        {{-- Register (Info Only) --}}
        <a href="#" title="Signup" data-bs-toggle="modal" data-bs-target="#registerModal">
            <i class="bi bi-person-plus-fill"></i>
        </a>

        <a href="#" title="Tips" data-bs-toggle="modal" data-bs-target="#tipsModal">
            <i class="bi bi-lightbulb-fill"></i>
        </a>
    </div>

    {{-- ✅ Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info text-center">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    {{-- ⚠️ Updated Register Modal --}}
    <div class="modal" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Registration Notice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-0">
                        🚫 You <strong>cannot register online</strong>.<br>
                        Please visit the <strong>MDRRMO office</strong> to complete your registration.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tips Modal --}}
    <div class="modal" id="tipsModal" tabindex="-1" aria-labelledby="tipsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tipsModalLabel">About This Website</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        This system helps <strong>accident victims</strong> quickly alert emergency responders
                        in the municipalities of <strong>Bantayan</strong>, <strong>Santa Fe</strong> and
                        <strong>Madridejos</strong> without needing to call phone numbers.
                    </p>
                    <ul class="mb-0">
                        <li>Report an accident and choose the municipality where it happened.</li>
                        <li>The selected responder team receives the report instantly and can confirm the incident.</li>
                        <li>Once on scene, responders can <strong>scan the victim’s fingerprint</strong>
                            to immediately view their full name, address, and emergency contact details.</li>
                        <li>This allows responders to provide faster assistance and notify the victim’s
                            emergency contact without delay.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            nonce="{{ $cspNonce }}"
            crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>

    <script nonce="{{ $cspNonce }}">
        // Prevent iframe embedding
        if (window.top !== window.self) window.top.location = window.self.location;
    </script>
</body>
</html>
