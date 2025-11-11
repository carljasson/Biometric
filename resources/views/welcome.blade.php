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

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous" referrerpolicy="no-referrer">

    {{-- Landing Page Styles --}}
    <style nonce="{{ $cspNonce }}">
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('{{ asset('images/background.png') }}') center center / cover no-repeat fixed;
            color: white;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.65);
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .landing-card {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 40px;
            max-width: 900px;
            width: 100%;
            text-align: center;
            box-shadow: 0 0 20px rgba(0,0,0,0.6);
            backdrop-filter: blur(8px);
        }

        .landing-card .logo img {
            max-width: 180px;
            margin-bottom: 20px;
        }

        .landing-card h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.7);
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
        }

        .feature-item {
            flex: 1 1 200px;
            max-width: 250px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            transition: transform 0.3s, background 0.3s;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .feature-item i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #ffd700;
        }

        .feature-item h5 {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .btn-home {
            margin-top: 30px;
            padding: 12px 30px;
            font-size: 1.2rem;
            border-radius: 50px;
        }

        @media(max-width: 768px) {
            .landing-card h1 { font-size: 2rem; }
            .feature-item { flex: 1 1 100%; max-width: 100%; }
        }
    </style>
</head>

<body>
    <div class="overlay">
        <div class="landing-card">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Biometric Medical Access Logo" loading="lazy" decoding="async">
            </div>

            <h1>Welcome to Biometric Medical Access</h1>
            <p>
                A system that allows <strong>accident victims</strong> to instantly alert emergency responders in <strong>Bantayan</strong>, <strong>Santa Fe</strong>, and <strong>Madridejos</strong>.
            </p>

            {{-- Features with Icons --}}
            <div class="features">
                <div class="feature-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h5>Report Accidents</h5>
                    <p>Notify responders immediately by choosing your municipality.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <h5>Instant Response</h5>
                    <p>The responder team receives the report instantly and confirms the incident.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-fingerprint"></i>
                    <h5>Biometric Scan</h5>
                    <p>Responders can scan the victim’s fingerprint to access their info quickly.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-phone-alt"></i>
                    <h5>Emergency Contact</h5>
                    <p>Notify the victim’s emergency contact without delay for faster assistance.</p>
                </div>
            </div>

            <a href="/" class="btn btn-primary btn-home"><i class="fas fa-home"></i> Go to Dashboard</a>
        </div>
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
