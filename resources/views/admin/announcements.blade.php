{{-- resources/views/admin/announcements.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Announcements - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">📢 Broadcast Announcement</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.announcement.add') }}">
    @csrf
    <div class="mb-3">
        <input type="text" name="title" class="form-control" placeholder="Announcement Title" required>
    </div>
    <div class="mb-3">
        <textarea name="message" class="form-control" rows="3" placeholder="Type your announcement..." required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Broadcast</button>
</form>

    <hr>

    <h4 class="mt-4">📝 Previous Announcements</h4>
    @forelse($announcements as $announcement)
        <div class="alert alert-secondary mt-2">
            <strong>{{ $announcement->created_at->format('F d, Y h:i A') }}</strong><br>
            {{ $announcement->message }}
        </div>
    @empty
        <p class="text-muted">No announcements yet.</p>
    @endforelse
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
