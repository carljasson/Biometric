@extends('layouts.patients')

@section('content')
<div class="container mt-4">

    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">← Back</a>
    </div>

    <h2 class="text-center text-danger mb-4">🚨 Emergency Contacts</h2>

    <!-- Emergency Alert Button (Triggers Modal) -->
    <div class="text-center mb-4">
        <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#emergencyModal">
            🚨 Send Emergency Alert
        </button>
    </div>

    <!-- Emergency Modal -->
    <div class="modal fade" id="emergencyModal" tabindex="-1" aria-labelledby="emergencyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="alertForm" action="{{ route('patient.sendAlert') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="emergencyModalLabel">Emergency Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        <!-- Select Emergency Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Type of Emergency</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="" disabled selected>Select type</option>
                                <option value="Medical">🩺 Medical</option>
                                <option value="Fire">🔥 Fire</option>
                                <option value="Crime">🚔 Crime</option>
                                <option value="Accident">🚑 Accident</option>
                            </select>
                        </div>

                        <!-- Camera Preview & Capture -->
                        <div class="mb-3">
                            <label class="form-label">📸 Capture Photo</label>
                            <div class="text-center">
                                <video id="camera" autoplay playsinline width="100%" class="rounded border"></video>
                                <canvas id="snapshot" style="display:none;"></canvas>
                                <input type="hidden" name="photo" id="photo">
                                <button type="button" class="btn btn-secondary mt-2" onclick="takeSnapshot()">Capture</button>
                            </div>
                            <div id="previewContainer" class="text-center mt-2" style="display:none;">
                                <img id="preview" class="img-fluid rounded border" />
                            </div>
                        </div>

                        <!-- Hidden location fields -->
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="address" id="address">

                        <div class="alert alert-info p-2 small" id="locationStatus">📍 Getting your location...</div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="submit" name="destination" value="Santa Fe" class="btn btn-danger">
                            🚨 Send to Santa Fe
                        </button>
                        <button type="submit" name="destination" value="Madridejos" class="btn btn-danger">
                            🚨 Send to Madridejos
                        </button>
                        <button type="submit" name="destination" value="Bantayan" class="btn btn-danger">
                            🚨 Send to Bantayan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- SweetAlert + Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ===== Location =====
    async function getUserLocation() {
        if (!navigator.geolocation) {
            document.getElementById('locationStatus').innerText = '❌ Geolocation not supported.';
            return;
        }

        navigator.geolocation.getCurrentPosition(async (position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lon;

            const apiKey = '45c8795c3e094eb8994cc238f809c663'; // 🔑 Replace with your actual OpenCage API key
            const apiUrl = `https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lon}&key=${apiKey}&language=en`;

            try {
                const response = await fetch(apiUrl);
                const data = await response.json();

                if (data && data.results && data.results.length > 0) {
                    const address = data.results[0].formatted;
                    document.getElementById('address').value = address;
                    document.getElementById('locationStatus').innerText = `📍 ${address}`;
                } else {
                    document.getElementById('locationStatus').innerText = '⚠️ Unable to retrieve address.';
                }
            } catch (error) {
                document.getElementById('locationStatus').innerText = '❌ Failed to get address.';
            }
        }, () => {
            document.getElementById('locationStatus').innerText = '⚠️ Location denied or unavailable.';
        });
    }

    // ===== Camera =====
    let video = document.getElementById('camera');
    let canvas = document.getElementById('snapshot');
    let preview = document.getElementById('preview');
    let previewContainer = document.getElementById('previewContainer');

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (err) {
            Swal.fire('Camera Error', 'Unable to access your camera.', 'error');
        }
    }

    function takeSnapshot() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/png');
        document.getElementById('photo').value = dataUrl;

        preview.src = dataUrl;
        previewContainer.style.display = "block";
    }

    // ===== Modal Events =====
    var emergencyModal = document.getElementById('emergencyModal');
    emergencyModal.addEventListener('shown.bs.modal', function () {
        getUserLocation();
        startCamera();
    });

    // ===== Confirm before sending =====
    document.getElementById('alertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const type = document.getElementById('type').value;
        const destination = e.submitter.value; // which button clicked

        if (!type) {
            Swal.fire('Select Emergency Type', 'Please choose an emergency type.', 'warning');
            return;
        }
        if (!document.getElementById('photo').value) {
            Swal.fire('Capture Required', 'Please capture a photo before sending.', 'warning');
            return;
        }

        Swal.fire({
            title: `Send ${type} Alert to ${destination}?`,
            text: "Are you sure you want to send this emergency alert?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, send it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    // ===== Success Toast =====
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: '{{ session('success') }}',
        toast: true,
        position: 'top-end',
        timer: 3000,
        showConfirmButton: false
    });
    @endif
</script>
@endsection
