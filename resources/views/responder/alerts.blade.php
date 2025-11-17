@foreach($alerts as $alert)
<div class="card shadow mb-3">
    <div class="card-body">

        <h5 class="fw-bold text-danger">🚨 {{ $alert->type }}</h5>

        <p><strong>Sender:</strong> {{ $alert->sender_name }}</p>
        <p><strong>Email:</strong> {{ $alert->sender_email }}</p>

        <p>
            <strong>Location:</strong>
            <a href="https://www.google.com/maps?q={{ $alert->latitude }},{{ $alert->longitude }}" 
               target="_blank" class="text-primary">
               View on Map
            </a>
        </p>

        <p><strong>Full Address:</strong> {{ $alert->address }}</p>

        <p><strong>Status:</strong> 
            <span class="badge bg-warning text-dark">{{ ucfirst($alert->status) }}</span>
        </p>

        <p class="text-muted">
            <strong>Sent:</strong> {{ $alert->created_at->diffForHumans() }}
        </p>

    </div>
</div>
@endforeach
