@extends('layouts.app')

@section('content')
<style>
/* ... your existing CSS ... */
</style>

<div id="mainContent">

@php
$roles = [
    'Admins' => $admins,
    'Responders' => $responders,
    'Users' => $users
];
@endphp

@foreach($roles as $roleName => $entries)
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-secondary fw-semibold">
                <i class="bi bi-clock-history me-2"></i> {{ $roleName }} Login History
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
                            <th>User</th>
                            <th>Method</th>
                            <th>IP Address</th>
                            <th>Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $e)
                            <tr>
                                <td class="text-center small">{{ $e->logged_in_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if($e->user)
                                        <strong>{{ $e->user->name }}</strong>
                                        <small class="text-muted d-block">ID: #{{ $e->user->id }}</small>
                                    @else
                                        <span class="text-muted fst-italic">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge 
                                        {{ $e->user->role == 'admin' ? 'bg-admin' : '' }}
                                        {{ $e->user->role == 'responder' ? 'bg-responder' : '' }}
                                        {{ $e->user->role == 'user' ? 'bg-user' : '' }}
                                        px-2 py-1 rounded">
                                        {{ ucfirst($e->method) }}
                                    </span>
                                </td>
                                <td><code class="small">{{ $e->ip }}</code></td>
                                <td class="text-truncate" style="max-width: 300px;" title="{{ $e->device }}">
                                    {{ $e->device }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-emoji-frown me-1"></i> No login history.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-2 px-3">
                {{ $entries->links() }}
            </div>
        </div>
    </div>
@endforeach

</div>
@endsection
