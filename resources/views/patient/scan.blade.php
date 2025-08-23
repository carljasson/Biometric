<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan - Biometric Medical Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-4">

    <!-- Back Button in Top Left -->
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-primary">← Back</a>
    </div>

    <h3 class="text-center mb-4">Biometric Scan</h3>

    <!-- Fingerprint Section -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Fingerprint Scan</div>
        <div class="card-body text-center">
            <img src="{{ asset('images/fingerprint.png') }}" alt="Fingerprint" width="120">
            <p class="mt-2">Tap the fingerprint to simulate scan.</p>
            <button class="btn btn-outline-primary" onclick="simulateFingerprint()">Scan Fingerprint</button>
            <input type="hidden" id="fingerprintData" name="fingerprint" value="">
            <p class="mt-2 text-success d-none" id="fpSuccess">Fingerprint scanned!</p>
        </div>
    </div>

    <!-- Face Scan Section -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Face Scan</div>
        <div class="card-body text-center">
            <video id="camera" width="240" height="180" autoplay></video>
            <canvas id="snapshot" width="240" height="180" class="d-none"></canvas>
            <p class="mt-2">Allow camera access and click capture.</p>
            <button class="btn btn-outline-success" onclick="captureFace()">Capture Face</button>
            <input type="hidden" id="faceData" name="face" value="">
            <p class="mt-2 text-success d-none" id="faceSuccess">Face captured!</p>
        </div>
    </div>

    <!-- Submit Scan -->
    <form action="{{ route('scan.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="fingerprint" id="hiddenFingerprint">
        <input type="hidden" name="face" id="hiddenFace">

        <!-- Submit Button -->
        <div class="d-grid">
            <button class="btn btn-dark">Submit Scans</button>
        </div>
    </form>
</div>

<script>
    // Open webcam
    const video = document.getElementById('camera');
    if (navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            });
    }

    function captureFace() {
        const canvas = document.getElementById('snapshot');
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, 240, 180);
        const dataURL = canvas.toDataURL();
        document.getElementById('hiddenFace').value = dataURL;
        document.getElementById('faceSuccess').classList.remove('d-none');
    }

    function simulateFingerprint() {
        const fakeFingerprint = 'sample_fingerprint_' + new Date().getTime();
        document.getElementById('hiddenFingerprint').value = fakeFingerprint;
        document.getElementById('fpSuccess').classList.remove('d-none');
    }
</script>

</body>
</html>
