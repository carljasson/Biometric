<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify PIN - Biometric Medical Access</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    @php $cspNonce = bin2hex(random_bytes(16)); @endphp

    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        base-uri 'self';
        form-action 'self';
        object-src 'none';
        frame-ancestors 'none';
        upgrade-insecure-requests;
        script-src 'self' https://cdn.jsdelivr.net 'nonce-{{ $cspNonce }}';
        style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net 'nonce-{{ $cspNonce }}';
        img-src 'self' data:;
        font-src 'self' data:;
    ">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          crossorigin="anonymous">

    <style nonce="{{ $cspNonce }}">
        body {
            background: url('{{ asset('images/background.png') }}') no-repeat center center fixed;
            background-size: cover;
            background-color: rgba(0,0,0,0.7);
            backdrop-filter: blur(3px);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .pin-container {
            background-color: rgba(255,255,255,0.4);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 40px 30px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }
        h4 { font-weight: bold; color: #333; margin-bottom: 20px; }
        input {
            text-align: center;
            letter-spacing: 6px;
            font-size: 22px;
            font-weight: bold;
        }
        button {
            margin-top: 15px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="pin-container">
    <img src="{{ asset('images/logo.png') }}" alt="Logo" width="90" class="mb-3">
    <h4>🔢 Verify Your PIN</h4>
    <p class="text-muted">A 6-digit PIN was sent to your email.<br>Please enter it below to continue.</p>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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

    <form method="POST" action="{{ route('verify.pin') }}">
        @csrf
        <div class="mb-3">
            <input type="text" name="pin" maxlength="6" class="form-control text-center" placeholder="Enter 6-digit PIN" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100">Verify PIN</button>
    </form>

    <div class="mt-3">
        <a href="{{ route('resend.pin') }}" class="text-decoration-none">Resend PIN</a> |
        <a href="{{ route('logout') }}" class="text-danger text-decoration-none">Cancel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" nonce="{{ $cspNonce }}"></script>

</body>
</html>
