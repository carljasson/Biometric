<!DOCTYPE html>
<html>

<head>
    <title>Biometric Medical Access</title>

    @php $cspNonce = bin2hex(random_bytes(16)); @endphp

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

    <!-- Disable zooming -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"
          crossorigin="anonymous" referrerpolicy="no-referrer">

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
            padding: 15px;
            overflow-y: auto;
        }

        .landing-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 25px 20px;
            width: 100%;
            max-width: 800px;
            text-align: center;
            box-shadow: 0 0 25px rgba(0,0,0,0.7);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .landing-card .logo img {
            max-width: 120px;
            width: 50%;
            height: auto;
            margin-bottom: 10px;
        }

        .landing-card h1 {
            font-size: 1.8rem;
            margin-bottom: 0.8rem;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .feature-item {
            flex: 1 1 100%;
            max-width: 220px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 12px;
            transition: transform 0.3s, background 0.3s;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.25);
        }

        .feature-item i {
            font-size: 1.5rem;
            margin-bottom: 8px;
            color: #ffd700;
        }

        .feature-item h5 {
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .feature-item p {
            font-size: 0.85rem;
        }

        .btn-home {
            margin-top: 20px;
            padding: 8px 20px;
            font-size: 0.95rem;
            border-radius: 50px;
        }

        /* Top-right menu */
        .top-menu {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.8rem;
            cursor: pointer;
            color: white;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.8);
            z-index: 20;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: 45px;
            right: 0;
            background-color: rgba(0,0,0,0.95);
            border-radius: 12px;
            overflow: hidden;
            display: none;
            flex-direction: column;
            min-width: 180px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .dropdown-menu-custom a {
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            display: block;
            transition: all 0.2s;
            font-weight: 500;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .dropdown-menu-custom a:last-child {
            border-bottom: none;
        }

        .dropdown-menu-custom a:hover {
            opacity: 0.9;
        }

        @media(max-width: 576px) {
            .landing-card {
                padding: 15px 10px;
            }
            .landing-card .logo img {
                max-width: 100px;
                width: 60%;
            }
            .landing-card h1 {
                font-size: 1.5rem;
            }
            .feature-item {
                flex: 1 1 100%;
                max-width: 100%;
                padding: 10px;
            }
            .feature-item i {
                font-size: 1.3rem;
            }
            .feature-item h5 {
                font-size: 0.9rem;
            }
            .feature-item p {
                font-size: 0.8rem;
            }
            .btn-home {
                font-size: 0.9rem;
                padding: 6px 15px;
            }
            .top-menu {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="overlay">
        <div class="landing-card">
            <!-- Top-right menu -->
            <div class="top-menu" id="menuToggle">
                <i class="fas fa-bars"></i>
                <div class="dropdown-menu-custom" id="dropdownMenu">
                    <a href="{{ route('responder.login') }}" style="background-color:#007bff;">Login as Responder</a>
                    <a href="{{ route('login') }}" style="background-color:#28a745;">Login as User</a>
                    <a href="#" id="registerLink" style="background-color:#dc3545;">Register</a>
                </div>
            </div>

            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Biometric Medical Access Logo" loading="lazy" decoding="async">
            </div>

            <h1>Biometric Medical Access</h1>
            <p>
                Instantly alert emergency responders in <strong>Bantayan</strong>, <strong>Santa Fe</strong>, and <strong>Madridejos</strong>.
            </p>

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
                    <p>Scan the victim’s fingerprint to access info quickly.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-phone-alt"></i>
                    <h5>Emergency Contact</h5>
                    <p>Notify contacts immediately for faster assistance.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            nonce="{{ $cspNonce }}"
            crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>

    <script nonce="{{ $cspNonce }}">
        // Prevent framing
        if (window.top !== window.self) window.top.location = window.self.location;

        // Disable pinch zoom on mobile
        document.addEventListener('touchmove', function (event) {
            if (event.scale !== undefined && event.scale !== 1) {
                event.preventDefault();
            }
        }, { passive: false });

        // Disable Ctrl + +/- and Ctrl + Mousewheel zoom on desktop
        window.addEventListener('wheel', function (e) {
            if (e.ctrlKey) e.preventDefault();
        }, { passive: false });

        window.addEventListener('keydown', function (e) {
            if (e.ctrlKey && (e.key === '+' || e.key === '-' || e.key === '=' || e.key === '0')) {
                e.preventDefault();
            }
        });

        // Toggle dropdown menu
        const menuToggle = document.getElementById('menuToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');

        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.style.display = dropdownMenu.style.display === 'flex' ? 'none' : 'flex';
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.style.display = 'none';
        });

        // Register popup
        document.getElementById('registerLink').addEventListener('click', function(e){
            e.preventDefault();
            alert('You cannot register here. Please register at MDRRMO.');
        });
    </script>
</body>
</html>
