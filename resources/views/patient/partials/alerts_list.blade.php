@if($alerts->isEmpty())
    <div class="alert alert-info text-center">You haven't sent any emergency alerts yet.</div>
@else
    @foreach($alerts as $alert)
        <div class="list-group-item mb-2 border rounded">
            <div class="d-flex justify-content-between">
                <span><strong>Type:</strong> {{ $alert->type }}</span>
                <span class="text-muted">{{ $alert->created_at->format('M d, Y H:i') }}</span>
            </div>
            <div><strong>Destination:</strong> {{ $alert->destination }}</div>
            <div><strong>Address:</strong> {{ $alert->address ?? '-' }}</div>
            @if($alert->photo)
                <div class="mt-2">
                    <img src="{{ asset($alert->photo) }}" class="img-fluid rounded border" style="max-height: 200px;">
                </div>
            @endif
            <div class="mt-1">
                <span class="badge bg-secondary">{{ $alert->status }}</span>
            </div>
        </div>
    @endforeach
@endif
