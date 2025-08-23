@extends('layouts.patients')

@section('content')
<div class="container mt-5 text-center text-dark">
        <!-- Back Button -->
    <a href="{{ route('responder.dashboard') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <h3>Responder: Fingerprint Scan</h3>
    <p>Tap the fingerprint icon to identify the user.</p>

    <i class="fas fa-fingerprint fa-5x text-success mt-4" style="cursor:pointer;" onclick="startScan()"></i>

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
async function startScan() {
    if (window.PublicKeyCredential) {
        try {
            const publicKey = {
                challenge: new Uint8Array(32), // Random 32 bytes
                rpId: window.location.hostname,
                timeout: 60000,
                userVerification: "required"
            };

            const credential = await navigator.credentials.get({ publicKey });
            const encoded = btoa(JSON.stringify(credential));

            document.getElementById("fingerprint_data").value = encoded;

            Swal.fire({
                title: 'Scanning...',
                text: 'Please wait...',
                timer: 1500,
                didOpen: () => Swal.showLoading()
            }).then(() => {
                document.getElementById("scanForm").submit();
            });

        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Scan Failed',
                text: 'Fingerprint scan failed or was cancelled. Please try again.',
            });
        }
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Unsupported Device',
            text: 'Your device/browser does not support fingerprint scan.',
        });
    }
}
</script>
@endsection
