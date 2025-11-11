@extends('layouts.app')

@section('content')
<div class="main-content flex-grow-1 p-4" style="margin-left: 16rem; min-height: 100vh; background-color: #f8f9fa;">

    <div class="card shadow-sm border-0 w-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 px-4 border-bottom">
            <h5 class="mb-0 text-secondary fw-semibold">
                <i class="bi bi-clock-history me-2"></i> Login History
            </h5>
            <small class="text-muted">
                Showing latest {{ $entries->count() }} entries
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: calc(100vh - 220px); overflow-y: auto;">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 18%;">Date & Time</th>
                            <th style="width: 25%;">User</th>
                            <th style="width: 15%;">Method</th>
                            <th style="width: 15%;">IP Address</th>
                            <th style="width: 27%;">Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $e)
                            <tr>
                                <td class="text-center small">
                                    <span class="badge bg-light text-dark fw-normal">
                                        {{ $e->logged_in_at->format('Y-m-d H:i:s') }}
                                    </span>
                                </td>
                                <td>
                                    @if($e->user)
                                        <strong>{{ $e->user->name }}</strong>
                                        <small class="text-muted d-block">ID: #{{ $e->user->id }}</small>
                                    @else
                                        <span class="text-muted fst-italic">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded">
                                        {{ ucfirst($e->method) }}
                                    </span>
                                </td>
                                <td><code class="small">{{ $e->ip }}</code></td>
                                <td class="text-truncate" title="{{ $e->device }}" style="max-width: 250px;">
                                    {{ $e->device }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
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

<style>
body {
    overflow-x: hidden;
}

.main-content {
    transition: margin-left 0.3s ease;
}

/* Table tweaks */
.table-hover tbody tr:hover {
    background-color: #f1f3f5 !important;
}

.badge.bg-primary-subtle {
    background-color: #e7f1ff !important;
    color: #0d6efd !important;
    font-size: 0.8rem;
}

/* Scrollbar styling */
.table-responsive::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background-color: #c1c1c1;
    border-radius: 10px;
}
</style>
@endsection
