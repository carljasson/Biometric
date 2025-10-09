@extends('layouts.patients')

@section('content')
<div class="container text-center mt-5">
    <h3>Launching Fingerprint App...</h3>
    <p>If it doesn’t open automatically, tap the icon below.</p>

    <a href="myfingerprint://scan" id="launchApp">
        <i class="fas fa-fingerprint fa-5x text-success mt-4"></i>
    </a>
</div>

<script>
    // Auto-launch the fingerprint app
    window.onload = function() {
        window.location.href = "myfingerprint://scan";
    };
</script>
@endsection
