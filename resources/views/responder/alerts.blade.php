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

                <h5 class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i> {{ $alert->type }}
                </h5>

                <p class="mb-1"><strong>Sender:</strong> {{ $alert->sender_name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $alert->sender_email }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $alert->sender_phone }}</p>
                <p class="mb-1"><strong>Destination:</strong> {{ $alert->destination }}</p>

                <p class="mb-2">
                    <strong>Location:</strong>
                    <a href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}"
                       target="_blank" class="text-primary">
                       📍 Open in Google Maps
                    </a>
                </p>

                @if($alert->photo)
                    <img src="{{ $alert->photo }}" class="img-fluid rounded mb-2"
                         style="max-height: 250px; border: 1px solid #ccc;">
                @endif

                <small class="text-muted">Received: {{ $alert->created_at->format('F j, Y h:i A') }}</small>
            </div>
        </div>
    @endforeach

</div>
@endsection
