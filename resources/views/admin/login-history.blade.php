@extends('layouts.admin')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <span>Recent Logins</span>
    <small class="text-muted">Showing latest {{ $entries->count() }} entries</small>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>Time</th>
          <th>User</th>
          <th>Method</th>
          <th>IP</th>
          <th>Device</th>
        </tr>
      </thead>
      <tbody>
        @forelse($entries as $e)
          <tr>
            <td>{{ $e->logged_in_at->format('Y-m-d H:i:s') }}</td>
            <td>
              @if($e->user)
                {{ $e->user->name }} <small class="text-muted">(#{{ $e->user->id }})</small>
              @else
                Unknown
              @endif
            </td>
            <td>{{ $e->method }}</td>
            <td>{{ $e->ip }}</td>
            <td>{{ \Illuminate\Support\Str::limit($e->device, 80) }}</td>
          </tr>
        @empty
          <tr><td colspan="5">No login records found.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="d-flex justify-content-end">
      {{ $entries->links() }}
    </div>
  </div>
</div>
@endsection
