@extends('layouts.responder')

@section('content')
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        background: url('/images/background.png') no-repeat center center fixed;
        background-size: cover;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(2px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-container {
        display: flex;
        max-width: 900px;
        width: 90%;
        border-radius: 12px;
        overflow: hidden;
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        position: relative;
        animation: fadeIn 0.6s ease-in-out;
    }

    .login-left, .login-right {
        flex: 1;
        padding: 40px;
    }

    .login-left {
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1px solid rgba(255,255,255,0.3);
    }

    .login-left img {
        max-width: 180px;
        height: auto;
    }

    .login-right {
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
    }

    /* X button */
    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        background-color: rgba(255,255,255,0.8);
        border-radius: 50%;
        text-align: center;
        line-height: 32px;
        font-weight: bold;
        font-size: 20px;
        color: #333;
        text-decoration: none;
        transition: 0.3s;
        z-index: 10;
    }
    .close-btn:hover {
        background-color: rgba(255,0,0,0.8);
        color: #fff;
    }

    .login-right h3 {
        font-weight: bold;
        color: white;
        margin-bottom: 30px;
        text-align: center;
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: none;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
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
    }

    .btn-primary:hover {
        background-color: #e2e6ea;
        color: #0a58ca;
    }

    .alert-danger {
        background-color: rgba(220, 53, 69, 0.9);
        border: none;
        color: white;
    }

    .text-danger {
        font-size: 0.875rem;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="login-container">

    <!-- Left: Logo -->
    <div class="login-left">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>

    <!-- Right: Login Form -->
    <div class="login-right">

        <!-- X Button -->
        <a href="{{ url('/') }}" class="close-btn">×</a>

        <h3>🚑 Emergency Responder Login</h3>

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

            {{-- Hidden input for reCAPTCHA token --}}
            <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>

</div>

{{-- ✅ Google reCAPTCHA v3 --}}
<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_SITEKEY') }}"></script>
<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ env('RECAPTCHA_SITEKEY') }}', {action: 'login'}).then(function(token) {
            document.getElementById('recaptchaResponse').value = token;
        });
    });
</script>
@endsection
