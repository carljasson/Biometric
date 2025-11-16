<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Biometric Medical Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #3498db, #8e44ad);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        .login-wrapper {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.15);
            width: 90%;
            max-width: 850px;
            display: flex;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        /* Left side with logo */
        .login-left {
            background: linear-gradient(135deg, #8e44ad, #3498db);
            color: #fff;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
        }

        .login-left img {
            width: 100px;
            height: 100px;
            margin-bottom: 15px;
        }

        .login-left h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-left p {
            font-size: 15px;
            opacity: 0.9;
        }

        /* Right side with form */
        .login-right {
            flex: 1;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .login-card {
            width: 100%;
            max-width: 320px;
        }

        .login-card h4 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 25px;
            color: #333;
        }

        .form-label { font-weight: 600; }
        .countdown { font-weight: bold; }

        .btn-primary {
            background: #3498db;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 400px;
            }
            .login-left {
                padding: 30px 20px;
            }
        }

        /* Disable text selection & zoom */
        * {
            touch-action: manipulation;
            user-select: none;
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <!-- LEFT SIDE -->
    <div class="login-left">
        <img src="{{ asset('images/logo.png') }}" alt="App Logo">
        <h2>Biometric Medical Access</h2>
        <p>Secure Admin Portal for Authorized Personnel</p>
    </div>

    <!-- RIGHT SIDE -->
    <div class="login-right">
        <div class="login-card">
            <h4>Admin Login</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('lockout'))
                <div class="alert alert-danger">
                    <div>{!! nl2br(e(session('lockout'))) !!}</div>
                    <div class="mt-1">Time remaining: <span id="lockout-countdown" class="countdown"></span></div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="login-form" action="{{ route('admin.login.submit') }}" method="POST" ...>

                @csrf

                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input id="email" type="email" name="email" class="form-control" required value="{{ old('email') }}" inputmode="email" autocomplete="username">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
                </div>

                <div class="d-grid">
                    <button id="login-btn" type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function(){
    const lockoutSeconds = @json(session('lockout_seconds', null));
    const countdownEl = document.getElementById('lockout-countdown');
    const loginForm = document.getElementById('login-form');
    const inputs = loginForm ? loginForm.querySelectorAll('input, button') : [];

    function disableForm(flag) {
        inputs.forEach(el => el.disabled = flag);
    }

    function formatMMSS(totalSeconds) {
        totalSeconds = Math.max(0, Math.floor(totalSeconds));
        const m = Math.floor(totalSeconds / 60).toString().padStart(2,'0');
        const s = (totalSeconds % 60).toString().padStart(2,'0');
        return `${m}:${s}`;
    }

    if (lockoutSeconds !== null) {
        disableForm(true);
        let remaining = Number(lockoutSeconds);
        if (countdownEl) countdownEl.textContent = formatMMSS(remaining);

        const tid = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(tid);
                if (countdownEl) countdownEl.textContent = '00:00';
                disableForm(false);
            } else {
                if (countdownEl) countdownEl.textContent = formatMMSS(remaining);
            }
        }, 1000);
    } else {
        disableForm(false);
        if (countdownEl) countdownEl.textContent = '';
    }
})();
</script>
</body>
</html>
