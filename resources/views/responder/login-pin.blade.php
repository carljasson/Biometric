@extends('layouts.responder')

@section('content')
<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
        <h4 class="text-center mb-3">Enter PIN</h4>

        @if(session('error'))
            <div class="alert alert-danger text-center">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <p class="text-center text-muted">A 6-digit PIN has been sent to your email.</p>

        <form method="POST" action="{{ route('responder.login.pin.submit') }}">
            @csrf
            <div class="mb-3">
                <input type="text" name="pin" class="form-control" placeholder="Enter PIN" required maxlength="6">
                @error('pin')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Verify PIN</button>
            </div>
        </form>

        {{-- Resend PIN Button --}}
        <div class="text-center mt-3">
            <a href="{{ route('responder.pin.resend') }}" class="btn btn-link">Resend PIN</a>
        </div>
    </div>
</div>
@endsection
