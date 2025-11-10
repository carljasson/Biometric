<!-- resources/views/auth/admin-login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #3498db, #8e44ad);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card { width:100%; max-width:400px; padding:20px; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,.1); }
        .form-label { font-weight: bold; }
        .logo { text-align:center; margin-bottom:15px; font-size:24px; color:#333; }
        .countdown { font-weight:700; }
    </style>
</head>
<body>
<div class="card bg-white">
    <div class="logo">Admin Login</div>

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

    <form id="login-form" action="{{ route('admin.login.post') }}" method="POST" autocomplete="off" novalidate>
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

<script>
(function(){
    // If server passed lockout_seconds, use it to run a real-time countdown
    const lockoutSeconds = @json(session('lockout_seconds', null));
    const countdownEl = document.getElementById('lockout-countdown');
    const loginForm = document.getElementById('login-form');
    const inputs = loginForm ? loginForm.querySelectorAll('input, button') : [];

    function disableForm(flag) {
        inputs.forEach(el => el.disabled = flag);
    }

    function formatHHMMSS(totalSeconds) {
        totalSeconds = Math.max(0, Math.floor(totalSeconds));
        const h = Math.floor(totalSeconds / 60).toString().padStart(2,'0');
        const m = Math.floor((totalSeconds % 60) / 60).toString().padStart(2,'0');
        const s = (totalSeconds % 60).toString().padStart(2,'0');
        return (h > 0 ? h + ':' + m + ':' + s : m + ':' + s);
    }

    if (lockoutSeconds !== null) {
        // Immediately disable form
        disableForm(true);
        let remaining = Number(lockoutSeconds);

        // show initial value
        if (countdownEl) countdownEl.textContent = formatHHMMSS(remaining);

        const tid = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(tid);
                if (countdownEl) countdownEl.textContent = '00:00';
                // re-enable form
                disableForm(false);
                // optionally refresh page to clear server-side session flag, or let user try again
                // location.reload();
            } else {
                if (countdownEl) countdownEl.textContent = formatHHMMSS(remaining);
            }
        }, 1000);
    } else {
        // No lockout — ensure form enabled
        disableForm(false);
        if (countdownEl) countdownEl.textContent = '';
    }
})();
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Biometric Medical Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background: #f8f9fa;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
        }

        /* Left side with logo or branding */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #3498db, #8e44ad);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
        }

        .login-left img {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
        }

        .login-left h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-left p {
            font-size: 15px;
            opacity: 0.9;
        }

        /* Right side with login form */
        .login-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
            padding: 40px;
        }

        .login-card {
            width: 100%;
            max-width: 380px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .form-label { font-weight: 600; }
        .countdown { font-weight: bold; }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .login-left {
                padding: 50px 20px;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <!-- LEFT SIDE -->
    <div class="login-left">
        <img src="{{ asset('images/logo.png') }}" alt="App Logo">
        <h2>Biometric Medical Access</h2>
        <p>Secure and seamless access for healthcare administrators.</p>
    </div>

    <!-- RIGHT SIDE -->
    <div class="login-right">
        <div class="login-card">
            <h4 class="text-center mb-3">Admin Login</h4>

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

            <form id="login-form" action="{{ route('admin.login.post') }}" method="POST" autocomplete="off" novalidate>
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

    function formatHHMMSS(totalSeconds) {
        totalSeconds = Math.max(0, Math.floor(totalSeconds));
        const m = Math.floor(totalSeconds / 60).toString().padStart(2,'0');
        const s = (totalSeconds % 60).toString().padStart(2,'0');
        return `${m}:${s}`;
    }

    if (lockoutSeconds !== null) {
        disableForm(true);
        let remaining = Number(lockoutSeconds);
        if (countdownEl) countdownEl.textContent = formatHHMMSS(remaining);

        const tid = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(tid);
                if (countdownEl) countdownEl.textContent = '00:00';
                disableForm(false);
            } else {
                if (countdownEl) countdownEl.textContent = formatHHMMSS(remaining);
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
