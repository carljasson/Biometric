@extends('layouts.responder')

@section('content')
@php $cspNonce = bin2hex(random_bytes(16)); @endphp

<style nonce="{{ $cspNonce }}">
body {
    background: url('{{ asset('images/background.png') }}') no-repeat center center fixed;
    background-size: cover;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(2px);
    min-height: 100vh;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: Arial, sans-serif;
    touch-action: manipulation;
}

.login-container {
    display: flex;
    background-color: rgba(255, 255, 255, 0.4);
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.25);
    max-width: 900px;
    width: 90%;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid rgba(255,255,255,0.6);
    animation: fadeIn 0.6s ease-in-out;
    flex-direction: row;
    position: relative;
}

.login-left, .login-right { flex: 1; padding: 40px; }
.login-left {
    display: flex;
    align-items: center;
    justify-content: center;
    border-right: 2px solid rgba(255,255,255,0.6);
    background-color: rgba(0,0,0,0.1);
}
.login-left img { max-width: 200px; height: auto; }

.login-right {
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    color: #333;
}

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
}
.close-btn:hover { background-color: rgba(255,0,0,0.8); color: #fff; }

.login-right h4 { font-weight: bold; margin-bottom: 30px; font-size: 1.2rem; text-align: center; }
.form-label { font-weight: 500; }
.alert { font-size: 0.9rem; }
.extra-links { font-size: 0.95rem; margin-top: 15px; }
.disabled-form { opacity: 0.5; pointer-events: none; }

.form-control {
    background-color: rgba(255,255,255,0.15);
    border: none;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    width: 100%;
    transition: all 0.3s ease;
}
.form-control::placeholder { color: rgba(0,0,0,0.5); }
.form-control:focus {
    background-color: rgba(255,255,255,0.25);
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

@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 768px) {
    .login-container { flex-direction: column; max-width: 95%; margin: 10px; }
    .login-left { border-right: none; border-bottom: 2px solid rgba(255,255,255,0.6); padding: 20px; }
    .login-left img { max-width: 150px; }
    .login-right { padding: 20px; }
    .login-right h4 { font-size: 1.2rem; }
    .btn, input { font-size: 1rem; min-height: 44px; }
}
</style>

<div class="login-container">
    <div class="login-left">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>

    <div class="login-right">
        <a href="javascript:void(0)" onclick="window.history.back();" class="close-btn">×</a>

        <h4>🚑 Emergency Responder Login</h4>

        @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        @if(session('lockout'))
            <div class="alert alert-danger text-center">
                Too many failed attempts. Wait <span id="lockout-timer">{{ session('lockout') }}</span> seconds.
            </div>
        @endif

        <form id="responder-login-form" method="POST" action="{{ route('responder.login.submit') }}"
            @if(session('lockout')) class="disabled-form" @endif>
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email Address" required>
            @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror

            <input type="password" name="password" class="form-control" placeholder="Password" required>
            @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror

            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary" @if(session('lockout')) disabled @endif>Login</button>
            </div>
        </form>

        <div class="text-center extra-links">
            <a href="{{ route('responder.password.request') }}">Forgot password?</a>
        </div>
    </div>
</div>

@if(session('lockout'))
<script nonce="{{ $cspNonce }}">
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
