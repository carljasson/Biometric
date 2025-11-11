@extends('layouts.responder')

@section('content')
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden; /* remove scroll bar */
        font-family: Arial, sans-serif;
    }

    body {
        background: url('/images/background.png') no-repeat center center fixed;
        background-size: cover;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(2px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .login-container {
        display: flex;
        max-width: 900px;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        animation: fadeIn 0.6s ease-in-out;
        align-items: stretch;
    }

    /* Left: Message */
    .login-left {
        flex: 1;
        background-color: rgba(0,0,0,0.2);
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: #fff;
        text-align: center;
        border-right: 1px solid rgba(255,255,255,0.2);
    }

    .login-left h2 {
        font-weight: bold;
        font-size: 1.8rem;
        margin-bottom: 15px;
    }

    .login-left p {
        font-size: 1.05rem;
        line-height: 1.5;
    }

    /* Right: Login Form */
    .login-right {
        flex: 1;
        padding: 40px 30px;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: white;
        background-color: rgba(0,0,0,0.15);
    }

    .login-right h3 {
        text-align: center;
        font-weight: bold;
        margin-bottom: 25px;
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 12px;
    }

    .form-control::placeholder {
        color: rgba(255,255,255,0.6);
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid #fff;
        box-shadow: none;
    }

    .btn-primary {
        background-color: #ffffff;
        color: #0d6efd;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        padding: 10px;
    }

    .btn-primary:hover {
        background-color: #e2e6ea;
        color: #0a58ca;
    }

    .alert-danger {
        background-color: rgba(220, 53, 69, 0.9);
        border: none;
        font-size: 0.9rem;
    }

    .text-danger {
        font-size: 0.875rem;
    }

    /* X / Close button */
    .close-button {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        line-height: 28px;
        text-align: center;
        cursor: pointer;
        transition: background 0.3s;
        z-index: 10;
    }

    .close-button:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
        }
        .login-left {
            border-right: none;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding: 30px 20px;
        }
        .login-right {
            padding: 30px 20px;
        }
    }
</style>

<div class="login-container">

    <!-- Left: Motivational Message -->
    <div class="login-left">
        <h2>🚑 Welcome, Hero!</h2>
        <p>Every second counts. Your bravery and quick action can save lives. Log in now and be ready to respond to emergencies and make a real difference in your community.</p>
    </div>

    <!-- Right: Login Form -->
    <div class="login-right">
        <!-- Close/X button -->
        <button class="close-button" onclick="window.location.href='{{ url('/') }}';">&times;</button>

        <h3>Responder Login</h3>

        @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        <form id="responder-login-form" method="POST" action="{{ route('responder.login.submit') }}">
            @csrf

            <div class="mb-3">
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email Address" required>
                @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                @error('password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</div>

{{-- Google reCAPTCHA v3 --}}
<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITEKEY') }}"></script>
<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ env('RECAPTCHA_SITEKEY') }}', {action: 'login'}).then(function(token) {
            document.getElementById('recaptchaResponse').value = token;
        });
    });
</script>
@endsection
