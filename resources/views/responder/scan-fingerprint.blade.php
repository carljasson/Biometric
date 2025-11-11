@extends('layouts.patients')

@section('content')
<div class="container mt-5 text-center text-dark">
    <!-- Back Button -->
    <a href="{{ route('responder.dashboard') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <h3>Responder: Fingerprint Scan</h3>
    <p>Tap the button below to identify the user.</p>

    <!-- Scan Button -->
    <a href="myfingerprint://scan" id="launchApp" class="btn btn-success btn-lg mt-4">
        <i class="fas fa-fingerprint me-2"></i> Scan
    </a>

    <form method="POST" action="{{ route('responder.scan.fingerprint.post') }}" id="scanForm">
        @csrf
        <input type="hidden" name="fingerprint_data" id="fingerprint_data">
    </form>

    @if(session('matched_user'))
        @php $u = session('matched_user'); @endphp
        <div class="alert alert-success mt-4">
            <h5>✅ Identity Found:</h5>
            <strong>Name:</strong> {{ $u->name }}<br>
            <strong>Email:</strong> {{ $u->email }}<br>
            <strong>Phone:</strong> {{ $u->phone }}
        </div>
    @elseif(session('not_found'))
        <div class="alert alert-danger mt-4">
            {{ session('not_found') }}
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Optional: Warn user if protocol is not registered
    document.getElementById('launchApp').addEventListener('click', function(e){
        console.log('Attempting to launch myfingerprint:// app');
    });
</script>
@endsection
