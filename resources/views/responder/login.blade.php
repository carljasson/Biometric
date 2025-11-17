@extends('layouts.responder')

@section('content')
<style>
    body {
        background: url('/images/background.png') no-repeat center center fixed;
        background-size: cover;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(2px);
        min-height: 100vh;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
        background-color: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 30px 25px;
        width: 90%;
        max-width: 400px;
        color: white;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        animation: fadeIn 0.6s ease-in-out;
        position: relative;
        text-align: center;
    }

    .login-card h3 {
        font-weight: bold;
        margin-bottom: 25px;
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.15);
        color: #fff;
        border: none;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.25);
        color: #fff;
        border: 1px solid #fff;
        box-shadow: none;
    }

    .btn-primary {
        background-color: #ffffff;
        color: #0d6efd;
        font-weight: bold;
        border-radius: 8px;
        padding: 10px 0;
        font-size: 1rem;
        transition: all 0.3s ease;
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

    .back-button {
        position: absolute;
        top: 20px;
        left: 20px;
        background-color: #007bff;
        color: white;
        padding: 8px 14px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.95rem;
        transition: background-color 0.3s;
        z-index: 10;
    }

    .back-button:hover {
        background-color: #0056b3;
    }

    .countdown-timer {
        text-align: center;
        margin-top: 10px;
        font-size: 0.95rem;
        color: #ffdddd;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Mobile adjustments */
    @media (max-width: 480px) {
        .login-card {
            padding: 25px 15px;
        }

        .btn-primary {
            font-size: 0.95rem;
        }
    }
</style>

<a href="javascript:void(0)" onclick="window.history.back(); return false;" class="back-button">← Back</a>

<div class="login-card text-white">

    <h3>🚑 Emergency Responder Login</h3>

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    @if(session('lockout'))
        <div class="countdown-timer">
            Too many failed attempts. Try again in <span id="lockout-timer">{{ session('lockout') }}</span> seconds.
        </div>
    @endif

    <form id="responder-login-form" method="POST" action="{{ route('responder.login.submit') }}"
          @if(session('lockout')) style="pointer-events:none; opacity:0.6;" @endif>
        @csrf

        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email Address" required>
        @error('email')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror

        <input type="password" name="password" class="form-control" placeholder="Password" required>
        @error('password')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror

        <div class="d-grid mt-3">
            <button type="submit" class="btn btn-primary" @if(session('lockout')) disabled @endif>Login</button>
        </div>
    </form>
</div>

@if(session('lockout'))
<script>
    let secondsLeft = {{ session('lockout') }};
    const timerEl = document.getElementById('lockout-timer');
    const form = document.getElementById('responder-login-form');
    const button = form.querySelector('button[type="submit"]');

    const timer = setInterval(() => {
        secondsLeft--;
        timerEl.textContent = secondsLeft;

        if (secondsLeft <= 0) {
            clearInterval(timer);
            form.style.pointerEvents = 'auto';
            form.style.opacity = '1';
            button.disabled = false;
        }
    }, 1000);
</script>
@endif
@endsection
