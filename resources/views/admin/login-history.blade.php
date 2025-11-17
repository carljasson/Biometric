@extends('layouts.app')

@section('content')
<style>
/* Make the main area adjust dynamically beside the sidebar */
#mainContent {
    transition: all 0.3s ease;
    min-height: 100vh;
    background-color: #f8f9fa;
    padding: 20px;
}

/* Full-width responsive card */
.card {
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    border: none;
}

/* Table adjustments */
.table {
    width: 100%;
    margin: 0;
}

.table th, .table td {
    vertical-align: middle !important;
}

.table thead {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 1;
}

/* Scrollable table */
.table-responsive {
    max-height: calc(100vh - 220px);
    overflow-y: auto;
}

/* Hover and badge styles */
.table-hover tbody tr:hover {
    background-color: #f1f3f5;
}
.badge.bg-primary-subtle {
    background-color: #e7f1ff !important;
    color: #0d6efd !important;
    font-size: 0.8rem;
}
.badge-role {
    font-size: 0.75rem;
    text-transform: uppercase;
    padding: 0.25rem 0.5rem;
}
</style>

<div id="mainContent">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-secondary fw-semibold">
                <i class="bi bi-clock-history me-2"></i> Login History
            </h5>
            <small class="text-muted">
                Showing latest {{ $entries->count() }} entries
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Date & Time</th>
                            <th>User / Role</th>
                            <th>Method</th>
                            <th>IP Address</th>
                            <th>Location</th>
                            <th>Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $e)
                            <tr>
                                <td class="text-center small">
                                    {{ $e->logged_in_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td>
                                    @php
    $actor = $e->loggable; // User, Admin, or Responder
@endphp

@if($actor)
    <strong>{{ $actor->name ?? $actor->username ?? 'Unknown' }}</strong>
    <small class="text-muted d-block">ID: #{{ $actor->id }}</small>

    <span class="badge bg-secondary badge-role">
        {{ class_basename($actor) }} {{-- Shows: User, Admin, Responder --}}
    </span>
@else
    <span class="text-muted fst-italic">Unknown</span>
@endif

                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle px-2 py-1 rounded">
                                        {{ ucfirst($e->method) }}
                                    </span>
                                </td>
                                <td><code class="small">{{ $e->ip }}</code></td>
                                <td>
                                    @if($e->location)
                                        {{ $e->location['city'] ?? '' }}, {{ $e->location['country'] ?? '' }}
                                    @else
                                        <span class="text-muted fst-italic">Unknown</span>
                                    @endif
                                </td>
                                <td class="text-truncate" style="max-width: 300px;" title="{{ $e->device }}">
                                    {{ $e->device }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown me-1"></i> No login history yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
