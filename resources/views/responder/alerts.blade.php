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
        <div class="alert alert-secondary">No emergency alerts at the moment.</div>
    @endif

    @foreach($alerts as $alert)
        <div class="card mb-3 shadow-sm" data-lat="{{ $alert->latitude }}" data-lng="{{ $alert->longitude }}">
            <div class="card-body">

                {{-- 🔥 ALERT TYPE --}}
                <h5 class="text-danger fw-bold">🚨 {{ $alert->type }}</h5>

                {{-- 👤 SENDER INFO --}}
                <p class="mb-1"><strong>Sender:</strong> {{ $alert->sender_name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $alert->sender_email }}</p>

                {{-- 📍 LOCATION --}}
                <p class="mb-2">
                    <strong>Location:</strong>
                    <a href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}"
                       target="_blank" class="text-primary">View on Map</a>
                </p>

                {{-- 🏠 ACCIDENT ADDRESS (NOT INPUT, AUTO DETECTED) --}}
                <p class="mb-1">
                    <strong>Accident Address:</strong>
                    <span class="accident-address text-primary">Loading...</span>
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
                    <button class="btn btn-primary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#reportModal{{ $alert->id }}">
                        🖨 Print Report
                    </button>

                    {{-- RESOLVE --}}
                    @if($alert->status !== 'Resolved')
                    <form id="resolve-form-{{ $alert->id }}" method="POST"
                          action="{{ route('responder.alerts.resolve', $alert->id) }}" style="display:none;">
                        @csrf
                    </form>

                    <button class="btn btn-success btn-sm resolve-btn" data-id="{{ $alert->id }}">
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

                        {{-- ⭐ ACCIDENT ADDRESS MOVED TO TOP --}}
                        <h5>📍 Accident Location</h5>
                        <p><strong>Accident Address:</strong> 
                            <span class="accident-address"></span>
                        </p>

                        <p><strong>Coordinates:</strong> {{ $alert->latitude }}, {{ $alert->longitude }}</p>
                        <p><strong>Sender:</strong> {{ $alert->sender_name }}</p>
                        <p><strong>Email:</strong> {{ $alert->sender_email }}</p>
                        <p><strong>Sent:</strong> {{ $alert->created_at }}</p>

                        <hr>

                        <h5>👤 Patient Information (Editable)</h5>

                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Firstname</label>
                                <input type="text" class="form-control" id="firstname{{ $alert->id }}" value="{{ $alert->firstname }}">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label">Middlename</label>
                                <input type="text" class="form-control" id="middlename{{ $alert->id }}" value="{{ $alert->middlename }}">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label">Lastname</label>
                                <input type="text" class="form-control" id="lastname{{ $alert->id }}" value="{{ $alert->lastname }}">
                            </div>

                            {{-- ⭐ NEW — PATIENT ADDRESS (INPUT FIELD) --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Patient Address</label>
                                <input type="text" class="form-control" id="patient_address{{ $alert->id }}" value="{{ $alert->address }}">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label">Age</label>
                                <input type="number" class="form-control" id="age{{ $alert->id }}" value="{{ $alert->age }}">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label">Gender</label>
                                <input type="text" class="form-control" id="gender{{ $alert->id }}" value="{{ $alert->gender }}">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="birthday{{ $alert->id }}" value="{{ $alert->birthday }}">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone{{ $alert->id }}" value="{{ $alert->phone }}">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="contact_name{{ $alert->id }}" value="{{ $alert->contact_name }}">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Emergency Contact Number</label>
                                <input type="text" class="form-control" id="contact_number{{ $alert->id }}" value="{{ $alert->contact_number }}">
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
/* ⭐ UPDATED PRINT FUNCTION */
function printReport(id) {

    let accidentAddress = document.querySelector(`#reportModal${id} .accident-address`).innerText;

    let html = `
        <h2>Accident Report</h2>
        <hr>

        <h4>Accident Location</h4>
        <p><strong>Accident Address:</strong> ${accidentAddress}</p>

        <hr>

        <h4>Patient Information</h4>
        <p><strong>Firstname:</strong> ${document.getElementById('firstname' + id).value}</p>
        <p><strong>Middlename:</strong> ${document.getElementById('middlename' + id).value}</p>
        <p><strong>Lastname:</strong> ${document.getElementById('lastname' + id).value}</p>
        <p><strong>Patient Address:</strong> ${document.getElementById('patient_address' + id).value}</p>

        <p><strong>Age:</strong> ${document.getElementById('age' + id).value}</p>
        <p><strong>Gender:</strong> ${document.getElementById('gender' + id).value}</p>
        <p><strong>Birthday:</strong> ${document.getElementById('birthday' + id).value}</p>
        <p><strong>Phone:</strong> ${document.getElementById('phone' + id).value}</p>
        <p><strong>Emergency Contact:</strong> ${document.getElementById('contact_name' + id).value}</p>
        <p><strong>Contact Number:</strong> ${document.getElementById('contact_number' + id).value}</p>
    `;

    let newWin = window.open('', '', 'width=800,height=900');
    newWin.document.write(`<html><head><title>Print Report</title></head><body>${html}</body></html>`);
    newWin.document.close();
    setTimeout(() => newWin.print(), 300);
}

/* Resolve Button */
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
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('resolve-form-' + id).submit();
            }
        });
    });
});

/* Reverse Geocoding -> Accident Address */
const apiKey = "45c8795c3e094eb8994cc238f809c663";

document.querySelectorAll('.card').forEach(card => {
    const lat = card.getAttribute('data-lat');
    const lng = card.getAttribute('data-lng');
    const addressEl = card.querySelector('.accident-address');

    if (lat && lng && addressEl) {
        fetch(`https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=${apiKey}`)
            .then(res => res.json())
            .then(data => {
                addressEl.innerText =
                    data?.results?.length ? data.results[0].formatted : "Address not found";

                // copy to modal also
                document.querySelectorAll('#reportModal{{ $alert->id }} .accident-address')
            })
            .catch(() => addressEl.innerText = "Error retrieving address");
    }
});
</script>
@endpush
