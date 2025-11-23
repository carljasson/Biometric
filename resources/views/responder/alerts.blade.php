@extends('layouts.responder')

@section('content')
<div class="container mt-4 mb-5">
    {{-- ⬅ Back Button --}}
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            &larr; Back
        </a>
    </div>

    <h3 class="mb-3">🚨 Emergency Alerts</h3>

    @if($alerts->count() == 0)
        <div class="alert alert-secondary">
            No emergency alerts at the moment.
        </div>
    @endif

    @foreach($alerts as $alert)
        <div class="card mb-3 shadow-sm" data-lat="{{ $alert->latitude }}" data-lng="{{ $alert->longitude }}">
            <div class="card-body">

                {{-- 🔥 ALERT TYPE --}}
                <h5 class="text-danger fw-bold">
                    🚨 {{ $alert->type }}
                </h5>

                {{-- 👤 SENDER INFO --}}
                <p class="mb-1"><strong>Sender:</strong> {{ $alert->sender_name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $alert->sender_email }}</p>

                {{-- 📍 LOCATION --}}
                <p class="mb-2">
                    <strong>Location:</strong>
                    <a href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}"
                       target="_blank" class="text-primary">
                       View on Map
                    </a>
                </p>

                {{-- 🏠 FULL ADDRESS --}}
                <p class="mb-1">
                    <strong>Full Address:</strong>
                    <span class="full-address text-primary">Loading...</span>
                </p>

                {{-- 🟡 STATUS --}}
                <p class="mb-1">
                    <strong>Status:</strong>
                    @if($alert->status !== 'Resolved')
                        <span class="badge bg-warning text-dark">{{ ucfirst($alert->status) }}</span>
                    @else
                        <span class="badge bg-success">Resolved</span>
                    @endif
                </p>

                {{-- ⏱ SENT TIME --}}
                <small class="text-muted">
                    <strong>Sent:</strong> {{ $alert->created_at->diffForHumans() }}
                </small>

                {{-- 📸 PHOTO --}}
                @if($alert->photo)
                    <div class="mt-3">
                        <img src="{{ asset($alert->photo) }}" class="img-fluid rounded"
                             style="max-height: 250px; border: 1px solid #ccc;">
                    </div>
                @endif

                {{-- BUTTONS --}}
                <div class="d-flex gap-2 mt-3">

                    {{-- ✅ PRINT REPORT BUTTON --}}
                    <button class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#reportModal{{ $alert->id }}">
                        🖨 Print Report
                    </button>

                    {{-- ✅ MARK AS RESOLVED BUTTON --}}
                    @if($alert->status !== 'Resolved')
                        <form id="resolve-form-{{ $alert->id }}" action="{{ route('responder.alerts.resolve', $alert->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('POST')
                        </form>
                        <button class="btn btn-success btn-sm resolve-btn"
                                data-id="{{ $alert->id }}">
                            ✅ Mark as Resolved
                        </button>
                    @endif
                </div>

            </div>
        </div>

        {{-- 📄 REPORT MODAL --}}
        <div class="modal fade" id="reportModal{{ $alert->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Accident Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" id="printArea{{ $alert->id }}">

                        <h4 class="text-danger fw-bold">🚨 {{ $alert->type }}</h4>

                        <hr>

                        <h5>📌 Alert Details</h5>
                        <p><strong>Sender:</strong> {{ $alert->sender_name }}</p>
                        <p><strong>Email:</strong> {{ $alert->sender_email }}</p>
                        <p><strong>Coordinates:</strong> {{ $alert->latitude }}, {{ $alert->longitude }}</p>
                        <p><strong>Address:</strong> <span class="full-address"></span></p>
                        <p><strong>Sent:</strong> {{ $alert->created_at }}</p>

                        <hr>

                        <h5>👤 User Information</h5>

                        <p><strong>Firstname:</strong> {{ $alert->firstname }}</p>
                        <p><strong>Middlename:</strong> {{ $alert->middlename }}</p>
                        <p><strong>Lastname:</strong> {{ $alert->lastname }}</p>
                        <p><strong>Address:</strong> {{ $alert->address }}</p>
                        <p><strong>Age:</strong> {{ $alert->age }}</p>
                        <p><strong>Gender:</strong> {{ $alert->gender }}</p>
                        <p><strong>Status:</strong> {{ $alert->status }}</p>
                        <p><strong>Birthday:</strong> {{ $alert->birthday }}</p>
                        <p><strong>Phone:</strong> {{ $alert->phone }}</p>
                        <p><strong>Contact Person:</strong> {{ $alert->contact_name }}</p>
                        <p><strong>Contact Number:</strong> {{ $alert->contact_number }}</p>

                        @if($alert->photo)
                        <hr>
                        <h5>📸 Attached Photo</h5>
                        <img src="{{ asset($alert->photo) }}" class="img-fluid rounded" style="max-height:250px;">
                        @endif

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                        {{-- PRINT BUTTON --}}
                        <button onclick="printReport('printArea{{ $alert->id }}')" class="btn btn-primary">
                            🖨 Print
                        </button>
                    </div>

                </div>
            </div>
        </div>

    @endforeach

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function printReport(areaId) {
    let content = document.getElementById(areaId).innerHTML;
    let printWindow = window.open('', '', 'height=900,width=800');
    printWindow.document.write('<html><head><title>Accident Report</title>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

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

    // Success Toast
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
    const apiKey = "45c8795c3e094eb8994cc238f809c663";

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
