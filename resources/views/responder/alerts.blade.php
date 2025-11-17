@extends('layouts.responder')

@section('content')
<div class="container mt-4 mb-5">

    <h3 class="mb-3">🚨 Emergency Alerts</h3>

    @if($alerts->count() == 0)
        <div class="alert alert-secondary">
            No emergency alerts at the moment.
        </div>
    @endif

    @foreach($alerts as $alert)
        <div class="card mb-3 shadow-sm" id="alertCard{{ $alert->id }}">
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
                <p class="mb-1"><strong>Full Address:</strong> {{ $alert->address }}</p>

                {{-- 🟡 STATUS --}}
                <p class="mb-1">
                    <strong>Status:</strong>
                    <span class="badge bg-{{ $alert->status == 'pending' ? 'warning' : ($alert->status == 'received' ? 'info' : 'success') }}">
                        {{ ucfirst($alert->status) }}
                    </span>
                </p>

                {{-- ⏱ SENT TIME --}}
                <small class="text-muted">
                    <strong>Sent:</strong> {{ $alert->created_at->diffForHumans() }}
                </small>

                {{-- 📸 PHOTO --}}
                @if($alert->photo)
                    <div class="mt-3">
                        <img src="{{ $alert->photo }}" class="img-fluid rounded"
                             style="max-height: 250px; border: 1px solid #ccc;">
                    </div>
                @endif

                {{-- ✅ ACTION BUTTONS --}}
                @if($alert->status == 'pending')
                    <div class="mt-3">
                        <button class="btn btn-sm btn-info me-2" onclick="updateAlertStatus({{ $alert->id }}, 'received')">
                            ✅ Received
                        </button>
                        <button class="btn btn-sm btn-success" onclick="updateAlertStatus({{ $alert->id }}, 'resolved')">
                            ✔️ Resolve
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endforeach

</div>

{{-- AJAX for status update --}}
<script>
function updateAlertStatus(alertId, status) {
    fetch("{{ route('responder.alerts.updateStatus') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ alert_id: alertId, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Update badge in UI
            const card = document.getElementById('alertCard' + alertId);
            const badge = card.querySelector('.badge');
            badge.innerText = status.charAt(0).toUpperCase() + status.slice(1);
            badge.className = 'badge bg-' + (status == 'received' ? 'info' : 'success');

            // Remove buttons if resolved
            if(status == 'resolved') {
                card.querySelectorAll('button').forEach(btn => btn.remove());
            }

            Swal.fire({
                icon: 'success',
                title: 'Status Updated!',
                text: `Alert marked as ${status}. Sender has been notified.`
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update status. Try again.'
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred.'
        });
    });
}
</script>
@endsection
