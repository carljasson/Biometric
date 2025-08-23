<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 3 - Face Scan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        video {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            clip-path: circle(50%);
            object-fit: cover;
            z-index: 1;
        }

        .overlay {
            position: absolute;
            border: 4px solid #00e676;
            border-radius: 50%;
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 20px rgba(0, 230, 118, 0.6);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 10px rgba(0, 230, 118, 0.3); }
            50% { box-shadow: 0 0 30px rgba(0, 230, 118, 0.8); }
            100% { box-shadow: 0 0 10px rgba(0, 230, 118, 0.3); }
        }

        .controls {
            position: absolute;
            bottom: 30px;
            width: 100%;
            text-align: center;
        }

        button {
            padding: 14px 28px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            background-color: #00c853;
            color: white;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 200, 83, 0.3);
            margin: 5px;
        }

        .note {
            color: #aaa;
            margin-top: 8px;
            font-size: 14px;
        }

        #detection-status {
            position: absolute;
            top: 20px;
            text-align: center;
            font-size: 16px;
            color: #00e676;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 6px 12px;
            border-radius: 8px;
            z-index: 999;
        }
    </style>
</head>
<body>

<div id="detection-status">🔍 Detecting face...</div>
<video id="video" autoplay muted playsinline></video>
<div class="overlay"></div>

<div class="controls">
    <form method="POST" action="{{ url('/register/step3') }}" id="faceForm">
        @csrf
        <input type="hidden" name="face_descriptor" id="face_descriptor">
        <input type="hidden" name="face_image" id="face_image">
        <button type="button" onclick="toggleCamera()">🔄 Switch Camera</button>
    </form>

    <form method="POST" action="{{ url('/register/step3') }}">
        @csrf
        <button type="submit" name="action" value="skip" style="background: #999;">Skip Face Scan</button>
    </form>

    <div class="note">Please align your face within the circle to continue.</div>
</div>

<canvas id="canvas" style="display:none;"></canvas>

<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const video = document.getElementById('video');
    const faceInput = document.getElementById('face_descriptor');
    const imageInput = document.getElementById('face_image');
    const canvas = document.getElementById('canvas');
    const statusText = document.getElementById('detection-status');

    let currentStream;
    let useFrontCamera = true;
    let scanning = false;
    let faceCaptured = false;

    async function loadModels() {
        await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector');
        await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition');
        await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68');
        console.log("✅ FaceAPI models loaded");
    }

    async function startCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        try {
            currentStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: useFrontCamera ? 'user' : 'environment' }
            });
            video.srcObject = currentStream;
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Camera Error', text: err.message });
        }
    }

    function toggleCamera() {
        useFrontCamera = !useFrontCamera;
        startCamera();
    }

    async function autoScanLoop() {
        if (!scanning || faceCaptured) return;

        const result = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (result && result.descriptor.length === 128) {
            const { box } = result.detection;

            const centerX = video.videoWidth / 2;
            const centerY = video.videoHeight / 2;
            const faceCenterX = box.x + box.width / 2;
            const faceCenterY = box.y + box.height / 2;

            const distanceFromCenter = Math.sqrt(
                Math.pow(centerX - faceCenterX, 2) +
                Math.pow(centerY - faceCenterY, 2)
            );

            const acceptableDistance = 80;
            const minFaceWidth = 100;
            const maxFaceWidth = 300;

            if (distanceFromCenter > acceptableDistance || box.width < minFaceWidth || box.width > maxFaceWidth) {
                statusText.textContent = "⚠️ Center your face in the circle.";
                setTimeout(autoScanLoop, 300);
                return;
            }

            faceCaptured = true;
            statusText.textContent = "✅ Face Captured! Submitting...";

            faceInput.value = JSON.stringify(Array.from(result.descriptor));
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            imageInput.value = canvas.toDataURL('image/jpeg');

            Swal.fire({ icon: 'success', title: 'Face Captured', showConfirmButton: false, timer: 1000 });

            setTimeout(() => {
                document.getElementById('faceForm').submit();
            }, 1000);
        } else {
            statusText.textContent = "🔍 Detecting face...";
            setTimeout(autoScanLoop, 300);
        }
    }

    window.addEventListener('DOMContentLoaded', async () => {
        await loadModels();
        await startCamera();
        scanning = true;
        autoScanLoop();
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session("success") }}',
        confirmButtonColor: '#00c853'
    }).then(() => window.location.href = "{{ url('/welcome') }}");
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session("error") }}'
    });
</script>
@endif

@if(session('info'))
<script>
    Swal.fire({
        icon: 'info',
        title: 'Skipped',
        text: '{{ session("info") }}'
    }).then(() => window.location.href = "{{ url('/welcome') }}");
</script>
@endif

</body>
</html>
