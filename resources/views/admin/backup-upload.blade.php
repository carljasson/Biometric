@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h2 class="mb-3">📁 Upload System Backup</h2>
    <p class="text-muted">Upload a ZIP, SQL, or any backup file so you can restore your system if it gets deleted or attacked.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm p-4">
        <form action="{{ route('admin.backup.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label class="form-label fw-bold">Select Backup File:</label>
            <input type="file" name="backup_file" class="form-control mb-3" required>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-cloud-upload"></i> Upload Backup
            </button>
        </form>
    </div>

</div>
@endsection
