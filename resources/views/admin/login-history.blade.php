@extends('layouts.app')

@section('content')

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-secondary">
            <i class="bi bi-clock-history me-1"></i> Login History
        </h6>
        <small class="text-muted">
            Showing latest {{ $entries->count() }} entries
        </small>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 20%;">Date & Time</th>
                        <th style="width: 25%;">User</th>
                        <th style="width: 15%;">Method</th>
                        <th style="width: 15%;">IP Address</th>
                        <th style="width: 25%;">Device</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $e)
                        <tr>
                            <td class="text-center">
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
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ ucfirst($e->method) }}
                                </span>
                            </td>
                            <td><code>{{ $e->ip }}</code></td>
                            <td>{{ \Illuminate\Support\Str::limit($e->device, 80) }}</td>
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

{{-- Optional custom CSS for better polish --}}
<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa !important;
    transition: 0.2s ease-in-out;
}

.card-header {
    border-bottom: 1px solid #dee2e6;
}

.badge.bg-primary-subtle {
    background-color: #e7f1ff;
    color: #0d6efd;
}
</style>

{{-- Include Bootstrap Icons if not yet in layout --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
