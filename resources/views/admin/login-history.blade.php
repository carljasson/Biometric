@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Logins</h5>
            <span class="text-muted">Showing latest {{ $entries->count() }} entries</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Time</th>
                            <th scope="col">User</th>
                            <th scope="col">Method</th>
                            <th scope="col">IP Address</th>
                            <th scope="col">Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($entry->logged_in_at)->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if($entry->user)
                                    {{ $entry->user->name }} <small class="text-muted">(#{{ $entry->user->id }})</small>
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td>{{ ucfirst($entry->method) }}</td>
                            <td>{{ $entry->ip }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($entry->device, 60) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No login entries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
