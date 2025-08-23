@extends('layouts.app')
@section('title', 'Emergency Alerts')

@section('content')
<div class="container mt-4">
    <h2 class="text-danger text-center mb-4">📢 Emergency Alerts</h2>

    @foreach ($alerts as $alert)
    <div class="card mb-3" data-lat="{{ $alert->latitude }}" data-lng="{{ $alert->longitude }}">
        <div class="card-body">
            <h5 class="card-title text-danger">🚨 {{ $alert->type }} Emergency</h5>
            <p class="card-text">
                <strong>Sender:</strong>
                {{ $alert->user->firstname ?? 'Unknown' }}
                {{ $alert->user->middlename ?? '' }}
                {{ $alert->user->lastname ?? '' }}<br>

                <strong>Email:</strong> {{ $alert->user->email ?? 'N/A' }}<br>

                <strong>Location:</strong>
                <a href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}" target="_blank">
                    View on Map
                </a><br>

                <strong>Full Address:</strong>
                <span class="full-address text-primary">Loading...</span><br>

                <strong>Status:</strong> {{ $alert->status }}
            </p>
            <small class="text-muted">Sent: {{ $alert->created_at->diffForHumans() }}</small>

            @if ($alert->status !== 'Resolved')
            <form id="resolve-form-{{ $alert->id }}" action="{{ route('admin.alerts.resolve', $alert->id) }}" method="POST" style="display: none;">
                @csrf
                @method('POST') {{-- Your route should support POST --}}
            </form>
            <button class="btn btn-sm btn-success mt-2 resolve-btn" data-id="{{ $alert->id }}">
                ✅ Mark as Resolved
            </button>
            @else
            <span class="badge bg-success mt-2">Resolved</span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // SweetAlert Resolve Logic
        document.querySelectorAll('.resolve-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                Swal.fire({
                    title: 'Mark as Resolved?',
                    text: 'This will mark the alert as handled.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, resolve it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('resolve-form-' + id).submit();
                    }
                });
            });
        });

        @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: @json(session('success')),
            showConfirmButton: false,
            timer: 2000
        });
        @endif

        // Reverse Geocoding to Get Full Address
        const apiKey = "45c8795c3e094eb8994cc238f809c663"; // 🔐 Replace this with your actual OpenCage API key

        document.querySelectorAll('.card').forEach(card => {
            const lat = card.getAttribute('data-lat');
            const lng = card.getAttribute('data-lng');
            const locationElement = card.querySelector('.full-address');

            if (lat && lng && locationElement) {
                fetch(`https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=${apiKey}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.results.length > 0) {
                            locationElement.innerText = data.results[0].formatted;
                        } else {
                            locationElement.innerText = "Address not found";
                        }
                    })
                    .catch(error => {
                        locationElement.innerText = "Error retrieving address";
                        console.error("Geocoding error:", error);
                    });
            }
        });
    });
</script>
@endpush
