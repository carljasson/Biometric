@extends('layouts.patients')

@section('content')
<style>
    #video {
        width: 250px;
        height: 250px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
    }

    .card {
        background-color: #f8f9fa;
        color: #000;
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .alert {
        margin-top: 20px;
    }

    #detection-status {
        font-size: 16px;
        margin-top: 10px;
        color: #007bff;
    }
</style>

<div class="container text-center mt-5">
    <!-- Back Button -->
    <a href="{{ route('responder.dashboard') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <h3>Responder Face Scan</h3>

    <div id="detection-status">🔍 Loading models...</div>
    <video id="video" autoplay muted playsinline></video>

    <form id="faceScanForm" method="POST" action="{{ route('responder.scan.face.identify') }}">
        @csrf
        <input type="hidden" name="face_descriptor" id="face_descriptor">
    </form>

    {{-- ✅ Not Identified --}}
    @if(session('not_identified'))
        <div class="alert alert-danger">{{ session('not_identified') }}</div>
    @endif

    {{-- ✅ Identified User --}}
    @if(session('identified_user'))
        <div class="card mx-auto" style="max-width: 400px;">
            <h4 class="mb-3">Person Identified</h4>
            <p><strong>Name:</strong> {{ session('identified_user')->name }}</p>
            <p><strong>Email:</strong> {{ session('identified_user')->email }}</p>
            <p><strong>Contact:</strong> {{ session('identified_user')->contact }}</p>
            @if(session('identified_user')->photo)
                <img src="{{ asset('storage/' . session('identified_user')->photo) }}" alt="User Photo" class="img-fluid rounded" style="max-height: 200px;">
            @endif
        </div>
    @endif
</div>

<script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const video = document.getElementById('video');
    const descriptorInput = document.getElementById('face_descriptor');
    const statusText = document.getElementById('detection-status');

    let scanning = true;

    async function loadModels() {
        await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector');
        await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68');
        await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition');
        statusText.textContent = "📷 Starting camera...";
    }

    async function startCamera() {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        statusText.textContent = "🔍 Detecting face...";
    }

    function isFaceCentered(box, width, height) {
        const centerX = width / 2;
        const centerY = height / 2;
        const faceCenterX = box.x + box.width / 2;
        const faceCenterY = box.y + box.height / 2;
        const distance = Math.hypot(centerX - faceCenterX, centerY - faceCenterY);
        return distance < 80 && box.width >= 100 && box.width <= 300;
    }

    async function detectFaceLoop() {
        if (!scanning) return;

        const detection = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (detection) {
            const box = detection.detection.box;
            const isCentered = isFaceCentered(box, video.videoWidth, video.videoHeight);

            if (!isCentered) {
                statusText.textContent = "⚠️ Please center your face.";
                return setTimeout(detectFaceLoop, 400);
            }

            // Capture descriptor and submit
            descriptorInput.value = JSON.stringify(Array.from(detection.descriptor));
            statusText.textContent = "✅ Face Captured! Submitting...";
            scanning = false;
            document.getElementById('faceScanForm').submit();
        } else {
            statusText.textContent = "🔍 Detecting face...";
            setTimeout(detectFaceLoop, 300);
        }
    }

    window.addEventListener('DOMContentLoaded', async () => {
        await loadModels();
        await startCamera();
        detectFaceLoop();
    });
</script>
@endsection
