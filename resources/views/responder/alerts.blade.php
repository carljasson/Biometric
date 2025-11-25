@extends('layouts.responder')

@push('styles')
<style>
/* Force modal and backdrop on top of everything */
.modal,
.modal-backdrop {
    z-index: 9999 !important;
}

.modal-dialog,
.modal-content {
    z-index: 10000 !important;
    position: relative;
}

/* Make sure buttons are clickable */
.modal .btn {
    position: relative;
    z-index: 10001 !important;
}

/* Sidebar below modal */
.sidebar {
    z-index: 1000 !important;
}

/* Bottom nav below modal */
.fixed-bottom-nav {
    z-index: 500 !important;
}

/* Transparent overlays do not block clicks */
.side-overlay {
    pointer-events: none !important;
    z-index: 0 !important;
}

</style>
@endpush


@section('content')

<div class="container mt-4 mb-5">

{{-- ⬅ Back Button --}}
<div class="mb-3">
    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">&larr; Back</a>
</div>

<h3 class="mb-3">🚨 Emergency Alerts</h3>

@if($alerts->count() == 0)
    <div class="alert alert-secondary">No emergency alerts at the moment.</div>
@endif

@foreach($alerts as $alert)
    <div class="card mb-3 shadow-sm" data-lat="{{ $alert->latitude }}" data-lng="{{ $alert->longitude }}" data-alert-id="{{ $alert->id }}">
        <div class="card-body">

            {{-- 🔥 ALERT TYPE --}}
            <h5 class="text-danger fw-bold">🚨 {{ $alert->type }}</h5>

            {{-- 👤 SENDER INFO --}}
            <p class="mb-1"><strong>Sender:</strong> {{ $alert->sender_name }}</p>
            <p class="mb-1"><strong>Email:</strong> {{ $alert->sender_email }}</p>

            {{-- 📍 LOCATION --}}
            <p class="mb-2">
                <strong>Location:</strong>
                <a href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}" target="_blank" class="text-primary">View on Map</a>
            </p>

            {{-- 🏠 ACCIDENT ADDRESS --}}
            <p class="mb-1">
                <strong>Accident Address:</strong>
                <span class="accident-address text-primary">Loading...</span>
            </p>

            {{-- 🟡 STATUS --}}
            <p class="mb-1">
                <strong>Status:</strong>

                @if($alert->status === 'Responder_on_the_way')
                    <span class="badge bg-success">Responder On The Way</span>
                @else
                    <span class="badge bg-warning text-dark">{{ ucfirst($alert->status) }}</span>
                @endif
            </p>

            {{-- ⏱ SENT TIME --}}
            <small class="text-muted"><strong>Sent:</strong> {{ $alert->created_at->diffForHumans() }}</small>

            {{-- 📸 PHOTO --}}
            @if($alert->photo)
                <div class="mt-3">
                    <img src="{{ asset($alert->photo) }}" class="img-fluid rounded" style="max-height:250px; border:1px solid #ccc;">
                </div>
            @endif

            {{-- BUTTONS --}}
            <div class="d-flex gap-2 mt-3">

                {{-- PRINT --}}
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal{{ $alert->id }}">🖨 Print Report</button>

                {{-- RESOLVE --}}
                @if($alert->status !== 'Responder_on_the_way')

                    <form id="resolve-form-{{ $alert->id }}" method="POST" action="{{ route('responder.alerts.resolve', $alert->id) }}" style="display:none;">
                        @csrf
                    </form>

                    <button class="btn btn-success btn-sm resolve-btn" data-id="{{ $alert->id }}">
                        ✅ Mark as Received
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

                    {{-- ACCIDENT ADDRESS --}}
                    <h5>📍 Accident Location</h5>
                    <p><strong>Accident Address:</strong> <span class="accident-address"></span></p>
                    <p><strong>Coordinates:</strong> {{ $alert->latitude }}, {{ $alert->longitude }}</p>
                    <p><strong>Sender:</strong> {{ $alert->sender_name }}</p>
                    <p><strong>Email:</strong> {{ $alert->sender_email }}</p>
                    <p><strong>Sent:</strong> {{ $alert->created_at }}</p>

                    <hr>

                    <h5>👤 Patient Information (Editable)</h5>

                    <div class="row">

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Firstname</label>
                            <input type="text" class="form-control" value="{{ $alert->firstname }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Middlename</label>
                            <input type="text" class="form-control" value="{{ $alert->middlename }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Lastname</label>
                            <input type="text" class="form-control" value="{{ $alert->lastname }}">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label class="form-label">Patient Address</label>
                            <input type="text" class="form-control" placeholder="Enter patient address">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" value="{{ $alert->age }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Gender</label>
                            <input type="text" class="form-control" value="{{ $alert->gender }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Birthday</label>
                            <input type="date" class="form-control" value="{{ $alert->birthday }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" value="{{ $alert->phone }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Emergency Contact Name</label>
                            <input type="text" class="form-control" value="{{ $alert->contact_name }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Emergency Contact Number</label>
                            <input type="text" class="form-control" value="{{ $alert->contact_number }}">
                        </div>
                    </div>

                    @if($alert->photo)
                        <hr>
                        <h5>📸 Attached Photo</h5>
                        <img src="{{ asset($alert->photo) }}" class="img-fluid rounded" style="max-height:250px;">
                    @endif

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button onclick="printReport({{ $alert->id }})" class="btn btn-primary">🖨 Print</button>
                    <button onclick="exportPDF({{ $alert->id }})" class="btn btn-danger">📄 Export PDF</button>
                </div>

            </div>
        </div>
    </div>

@endforeach

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
function printReport(id) {
    const modalBody = document.querySelector(`#reportModal${id} .modal-body`);
    if (!modalBody) return;

    const printWindow = window.open('', '', 'width=900,height=700');
    printWindow.document.write(`
        <html>
            <head>
                <title>Accident Report</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h2, h4 { color: #dc3545; }
                    hr { border: 1px solid #ccc; margin: 10px 0; }
                    p { margin: 5px 0; }
                    .bold { font-weight: bold; }
                </style>
            </head>
            <body>
                ${modalBody.innerHTML}
            </body>
        </html>
    `);
    printWindow.document.close();
    setTimeout(() => printWindow.print(), 300);
}

window.exportPDF = function(id) {
    const modalBody = document.querySelector(`#reportModal${id} .modal-body`);
    if (!modalBody) return;

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'pt', 'a4');

    doc.html(modalBody, {
        callback: function (pdf) {
            pdf.save(`Accident_Report_${id}.pdf`);
        },
        x: 20,
        y: 20,
        width: 555,
        windowWidth: modalBody.scrollWidth,
    });
}

document.querySelectorAll('.resolve-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.getAttribute('data-id');

        Swal.fire({
            title: 'Mark as Received?',
            text: 'This will update the status to: Responder is on the way.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('resolve-form-' + id).submit();
            }
        });
    });
});


// Reverse Geocoding
const apiKey = "45c8795c3e094eb8994cc238f809c663";

document.querySelectorAll('.card').forEach(card => {
    const lat = card.getAttribute('data-lat');
    const lng = card.getAttribute('data-lng');
    const alertId = card.getAttribute('data-alert-id');
    const addressEl = card.querySelector('.accident-address');

    if (lat && lng && addressEl) {
        fetch(`https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=${apiKey}`)
            .then(res => res.json())
            .then(data => {
                const formattedAddress = data?.results?.length ? data.results[0].formatted : "Address not found";
                addressEl.innerText = formattedAddress;

                // Also fill the modal
                const modalAddressEl = document.querySelector(`#reportModal${alertId} .accident-address`);
                if (modalAddressEl) modalAddressEl.innerText = formattedAddress;
            })
            .catch(() => addressEl.innerText = "Error retrieving address");
    }
});
</script>
@endpush
