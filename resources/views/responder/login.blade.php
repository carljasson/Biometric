@extends('layouts.responder')

@section('content')
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden; /* remove scroll bar */
    }

    body {
        background: url('/images/background.png') no-repeat center center fixed;
        background-size: cover;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(2px);
        display: flex;
        justify-content: center;
        align-items: center; /* vertically center */
        position: relative;
        font-family: Arial, sans-serif;
    }

    .login-card {
        background-color: #ffffff10;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 30px;
        width: 100%;
        max-width: 400px;
        color: white;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        animation: fadeIn 0.6s ease-in-out;
        position: relative;
    }

    .login-card h3 {
        font-weight: bold;
        margin-bottom: 25px;
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
</style>

<div class="login-card text-white">

    <!-- Close/X button -->
    <button class="close-button" onclick="window.location.href='{{ url('/') }}';">&times;</button>

    <h3 class="text-center">🚑 Emergency Responder Login</h3>

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
