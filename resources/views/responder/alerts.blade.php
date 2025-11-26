@extends('layouts.responder')
@section('title', 'Emergency Alerts')

@push('styles')
<style>
/* Modal always on top */
.modal,
.modal-backdrop {
    z-index: 10500 !important;
}

.modal-dialog,
.modal-content {
    position: relative;
    z-index: 10600 !important;
}

/* Resolve buttons above everything */
.resolve-btn {
    position: relative;
    z-index: 11000 !important;
}

/* Bottom nav below modals */
.fixed-bottom-nav {
    z-index: 1000 !important;
}
</style>
@endpush

@section('content')
<div class="container mt-4 mb-5">

    {{-- Back Button --}}
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">&larr; Back</a>
    </div>

    <h3 class="mb-3">🚨 Emergency Alerts</h3>

    @if($alerts->count() == 0)
        <div class="alert alert-secondary">No emergency alerts at the moment.</div>
    @endif

    @foreach($alerts as $alert)
    <div class="card mb-3 shadow-sm" data-lat="{{ $alert->latitude }}" data-lng="{{ $alert->longitude }}">
        <div class="card-body">
            <h5 class="text-danger fw-bold">🚨 {{ $alert->type }}</h5>

            <p class="mb-1"><strong>Sender:</strong> {{ $alert->sender_name }}</p>
            <p class="mb-1"><strong>Email:</strong> {{ $alert->sender_email }}</p>
            <p class="mb-2">
                <strong>Location:</strong>
                <a href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}" target="_blank" class="text-primary">View on Map</a>
            </p>
            <p class="mb-1">
                <strong>Accident Address:</strong>
                <span class="accident-address text-primary">Loading...</span>
            </p>
            <p class="mb-1">
                <strong>Status:</strong>
                @if($alert->status === 'Responder_on_the_way')
                    <span class="badge bg-success">Responder On The Way</span>
                @else
                    <span class="badge bg-warning text-dark">{{ ucfirst($alert->status) }}</span>
                @endif
            </p>
            <small class="text-muted"><strong>Sent:</strong> {{ $alert->created_at->diffForHumans() }}</small>

            @if($alert->photo)
            <div class="mt-3">
                <img src="{{ asset($alert->photo) }}" class="img-fluid rounded" style="max-height:250px; border:1px solid #ccc;">
            </div>
            @endif

            <div class="d-flex gap-2 mt-3">
                @if($alert->status !== 'Responder_on_the_way')
                    <form id="resolve-form-{{ $alert->id }}" method="POST" action="{{ route('responder.alerts.resolve', $alert->id) }}" style="display:none;">
                        @csrf
                    </form>
                    <button type="button" class="btn btn-success btn-sm resolve-btn" data-id="{{ $alert->id }}">✅ Mark as Received</button>
                @endif
            </div>
        </div>
    </div>
    @endforeach

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Mark as Received
    document.querySelectorAll('.resolve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                title: 'Mark as Received?',
                text: 'This will update the status to: Responder is on the way.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then(result => {
                if(result.isConfirmed){
                    document.getElementById('resolve-form-' + id).submit();
                }
            });
        });
    });

    // Reverse Geocoding
    const apiKey = "45c8795c3e094eb8994cc238f809c663";
    document.querySelectorAll('.card').forEach(card => {
        const lat = card.dataset.lat;
        const lng = card.dataset.lng;
        const addressEl = card.querySelector('.accident-address');
        if(lat && lng && addressEl){
            fetch(`https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=${apiKey}`)
            .then(res => res.json())
            .then(data => {
                const formattedAddress = data?.results?.length ? data.results[0].formatted : "Address not found";
                addressEl.innerText = formattedAddress;
            })
            .catch(() => addressEl.innerText = "Error retrieving address");
        }
    });
});
</script>
@endpush
