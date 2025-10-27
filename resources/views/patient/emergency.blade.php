@extends('layouts.patients')

@section('content')
    <style>
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
        }
        .bottom-nav a {
            color: #333;
            text-align: center;
            font-size: 14px;
            text-decoration: none;
        }
        .bottom-nav a i {
            font-size: 20px;
            display: block;
        }
    </style>
<div class="container mt-4">

    

    <h2 class="text-center text-danger mb-4">🚨 Emergency Contacts</h2>

    <!-- Emergency Alert Button (Triggers Modal) -->
    <div class="text-center mb-4">
        <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#emergencyModal">
            🚨 Send Emergency Alert
        </button>
    </div>

    <!-- Bottom Navigation -->
<div class="bottom-nav">
    <a href=="{{ route('dashboard') }}"><i class="fas fa-home"></i><br>Home</a>
        <a href="{{ route('emergency') }}"><i class="fas fa-phone-alt text-danger"></i><br>Emergency</a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><br>My Profile</a>

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

            const apiKey = '45c8795c3e094eb8994cc238f809c663'; // OpenCage API key
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
    let stream = null;

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (err) {
            Swal.fire('Camera Error', 'Unable to access your camera.', 'error');
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
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
    const emergencyModal = document.getElementById('emergencyModal');
    emergencyModal.addEventListener('shown.bs.modal', function () {
        getUserLocation();
        startCamera();
    });
    emergencyModal.addEventListener('hidden.bs.modal', function () {
        stopCamera();
    });

    // ===== Confirm before sending =====
    document.getElementById('alertForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const type = document.getElementById('type').value;
        const destination = e.submitter.value;

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
                const formData = new FormData(this);
formData.append('destination', destination); // ✅ manually include

                fetch("{{ route('patient.sendAlert') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name=\"_token\"]').value
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: "success",
                            title: data.message,
                            timer: 2500,
                            showConfirmButton: false
                        });
                        const modal = bootstrap.Modal.getInstance(document.getElementById('emergencyModal'));
                        modal.hide();
                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                })
                .catch(() => {
                    Swal.fire("Error", "Failed to send alert. Please try again.", "error");
                });
            }
        });
    });
</script>
@endsection
