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

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<a href="javascript:void(0)" onclick="window.history.back(); return false;" class="back-button">← Back</a>

<div class="login-card text-white">

    <h3 class="mb-4 text-center">🚑 Emergency Responder Login</h3>

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('responder.login.submit') }}">
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

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>
</div>
@endsection
