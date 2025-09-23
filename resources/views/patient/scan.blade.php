<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan - Biometric Medical Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-4">

    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-primary">← Back</a>
    </div>

    <h3 class="text-center mb-4">Biometric Scan</h3>

    <!-- Fingerprint Section -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Fingerprint Scan</div>
        <div class="card-body text-center">
            <!-- Make this a button that triggers the responder app -->
         <img src="{{ asset('images/fingerprint.png') }}" style="cursor:pointer" onclick="window.location.href='responderapp://start-scan'">

            <p class="mt-2">Click the fingerprint to start scanning.</p>
            <p class="mt-2 text-success d-none" id="fpSuccess">Fingerprint matched!</p>
            <div id="userInfo" class="mt-3"></div>
        </div>
    </div>

    <!-- Face Scan Section (unchanged) -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Face Scan</div>
        <div class="card-body text-center">
            <video id="camera" width="240" height="180" autoplay></video>
            <canvas id="snapshot" width="240" height="180" class="d-none"></canvas>
            <p class="mt-2">Allow camera access and click capture.</p>
            <button class="btn btn-outline-success" onclick="captureFace()">Capture Face</button>
            <input type="hidden" id="hiddenFace" name="face" value="">
            <p class="mt-2 text-success d-none" id="faceSuccess">Face captured!</p>
        </div>
    </div>

    <!-- Submit Scan -->
    <form action="{{ route('scan.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="fingerprint" id="hiddenFingerprint">
        <input type="hidden" name="face" id="hiddenFace">
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
            .then(stream => { video.srcObject = stream; });
    }

    function captureFace() {
        const canvas = document.getElementById('snapshot');
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, 240, 180);
        const dataURL = canvas.toDataURL();
        document.getElementById('hiddenFace').value = dataURL;
        document.getElementById('faceSuccess').classList.remove('d-none');
    }

    // ---------------------------
    // Trigger Responder App
    // ---------------------------
    async function startResponderScan() {
        try {
            // Make a GET request to the local responder app HTTP server
            const response = await axios.get('http://localhost:5000/start-scan/');
            console.log(response.data);
            alert('Responder app triggered! Place your finger on the scanner.');
        } catch(e) {
            console.error("Failed to trigger responder app:", e);
            alert("Unable to start scan. Make sure the responder app is running on this PC.");
        }
    }

    // ---------------------------
    // Polling for fingerprint scan result
    // ---------------------------
    async function checkFingerprint() {
        try {
            const response = await axios.get('/fingerprint/latest'); // Laravel returns latest scan
            if(response.data.status === 'success') {
                const user = response.data.user;
                document.getElementById('fpSuccess').classList.remove('d-none');
                document.getElementById('userInfo').innerHTML = `
                    <h5>${user.firstname} ${user.middlename} ${user.lastname}</h5>
                    <p>Address: ${user.address}</p>
                    <p>Phone: ${user.phone}</p>
                    <p>Gender: ${user.gender}</p>
                    <p>Age: ${user.age}</p>
                    <p>Emergency Contact: ${user.contact_name} - ${user.contact_number}</p>
                    <p>Status: ${user.status}</p>
                    <p>Birthday: ${user.birthday}</p>
                `;
                document.getElementById('hiddenFingerprint').value = response.data.fingerprint;
            }
        } catch(e) {
            console.error(e);
        }
        setTimeout(checkFingerprint, 1000); // check every 1 second
    }

    checkFingerprint();
</script>

</body>
</html>
