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
        <div class="modal-dialog">
            <form id="alertForm" action="{{ route('patient.sendAlert') }}" method="POST">
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

                        <!-- Hidden location fields -->
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="address" id="address">

                        <div class="alert alert-info p-2 small" id="locationStatus">📍 Getting your location...</div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Send Alert</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- SweetAlert + Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function getUserLocation() {
        if (!navigator.geolocation) {
            document.getElementById('locationStatus').innerText = '❌ Geolocation is not supported by your browser.';
            return;
        }

        navigator.geolocation.getCurrentPosition(async (position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lon;

            // 🔑 Replace with your actual OpenCage API key
            const apiKey = '45c8795c3e094eb8994cc238f809c663';
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
                console.error(error);
                document.getElementById('locationStatus').innerText = '❌ Failed to get address.';
            }

        }, (error) => {
            console.error(error);
            document.getElementById('locationStatus').innerText = '⚠️ Location access denied or unavailable.';
        });
    }

    // Trigger on modal show
    var emergencyModal = document.getElementById('emergencyModal');
    emergencyModal.addEventListener('shown.bs.modal', function () {
        getUserLocation();
    });

    // Confirm before sending alert
    document.getElementById('alertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const type = document.getElementById('type').value;

        if (!type) {
            Swal.fire('Select Emergency Type', 'Please choose an emergency type.', 'warning');
            return;
        }

        Swal.fire({
            title: `Send ${type} Alert?`,
            text: "Are you sure you want to send this emergency alert?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, send it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                setTimeout(() => this.submit(), 800); // Wait to ensure address is set
            }
        });
    });

    // Toast success message
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
