<small class="text-muted">Showing latest {{ $entries->count() }} entries</small>

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
<tr>
    <td colspan="5">No login history yet.</td>
</tr>
@endforelse
</tbody>
