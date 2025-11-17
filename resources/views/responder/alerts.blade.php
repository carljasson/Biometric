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
        <div class="card mb-3 shadow-sm">
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
                    <span class="badge bg-warning text-dark">{{ ucfirst($alert->status) }}</span>
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

            </div>
        </div>
    @endforeach

</div>
@endsection
