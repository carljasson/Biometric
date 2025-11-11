<!DOCTYPE html>
<html>

<head>
    <title>Biometric Medical Access</title>

    {{-- 🔐 Generate a nonce for CSP-safe inline <style> and <script> --}}
    @php $cspNonce = bin2hex(random_bytes(16)); @endphp

    {{-- 🔐 Security headers --}}
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        base-uri 'self';
        form-action 'self';
        object-src 'none';
        frame-ancestors 'none';
        upgrade-insecure-requests;
        script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'nonce-{{ $cspNonce }}';
        style-src  'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com 'nonce-{{ $cspNonce }}';
        img-src 'self' data:;
        font-src 'self' https://cdnjs.cloudflare.com;
        connect-src 'self';
    ">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="no-referrer">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap and FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">

    {{-- Centered Design --}}
    <style nonce="{{ $cspNonce }}">
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: white;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
            background: url('{{ asset('images/background.png') }}') center center / cover no-repeat fixed;
        }

        .center-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            padding: 20px;
            background: rgba(0,0,0,0.5);
        }

        .center-wrapper h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .center-wrapper p {
            font-size: 1.2rem;
            line-height: 1.6;
            max-width: 700px;
        }

        .highlight {
            font-weight: bold;
            color: #ffd700;
        }

        .logo img {
            max-width: 200px;
            margin-bottom: 2rem;
        }

        .btn-home {
            margin-top: 2rem;
            padding: 10px 25px;
            font-size: 1.1rem;
            border-radius: 50px;
        }

        @media(max-width: 576px) {
            .center-wrapper h1 { font-size: 2rem; }
            .center-wrapper p { font-size: 1rem; }
        }
    </style>
</head>

<body>
    {{-- Centered Content --}}
    <div class="center-wrapper">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Biometric Medical Access Logo" loading="lazy" decoding="async">
        </div>

        <h1>Welcome to Biometric Medical Access</h1>

        <p>
            This system helps <span class="highlight">accident victims</span> quickly alert emergency responders
            in the municipalities of <span class="highlight">Bantayan</span>, <span class="highlight">Santa Fe</span> and
            <span class="highlight">Madridejos</span> without needing to call phone numbers.
        </p>
        <p>
            <span class="highlight">Report an accident</span> and choose the municipality where it happened. The selected
            responder team receives the report instantly and can confirm the incident. Once on scene, responders can
            <span class="highlight">scan the victim’s fingerprint</span> to immediately view their full name, address,
            and emergency contact details. This allows responders to provide faster assistance and notify the victim’s
            emergency contact without delay.
        </p>

        <a href="/" class="btn btn-primary btn-home"><i class="fas fa-home"></i> Go to Dashboard</a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info text-center">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

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
