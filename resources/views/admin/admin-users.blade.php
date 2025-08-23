@extends('layouts.app')
@section('title', 'Admin User Management')

@section('content')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .btn-info {
        background-color: #0dcaf0;
        color: white;
    }
    .btn-info:hover {
        background-color: #0bbcd4;
        color: white;
    }
    .record-header {
        background-color: #007bff;
        color: white;
        padding: 20px;
        border-radius: 8px 8px 0 0;
    }
    .record-container {
        background: rgba(255,255,255,0.95);
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
</style>

<div class="container mt-4">
    <div class="record-container">
        <div class="record-header mb-3 d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="bi bi-person-fill-gear"></i> Manage Admin Accounts</h3>
            <button class="btn btn-light text-dark" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="bi bi-person-plus-fill"></i> Add Admin
            </button>
        </div>

        @if($admins->isEmpty())
            <div class="alert alert-warning m-3">
                <i class="bi bi-info-circle-fill"></i> No admin accounts found.
            </div>
        @else
            <div class="table-responsive p-3">
                <table class="table table-hover table-bordered">
                    <thead class="table-light text-center">
                        <tr>
                            <th><i class="bi bi-person-badge-fill"></i> Name</th>
                            <th><i class="bi bi-envelope-fill"></i> Email</th>
                            <th><i class="bi bi-shield-lock-fill"></i> Role</th>
                            <th><i class="bi bi-gear-fill"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->role === 'superadmin' ? 'Super Admin' : 'Admin' }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.destroy', $admin->id) }}" class="d-inline delete-admin-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-name="{{ $admin->name }}">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input name="name" class="form-control mb-2" placeholder="Full Name" required>
                <input name="email" class="form-control mb-2" placeholder="Email" required>
                <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
                <select name="role" class="form-select mb-2">
                    <option value="admin">Admin</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Add Admin</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modals -->
@foreach($admins as $admin)
<div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.update', $admin->id) }}" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input name="name" value="{{ $admin->name }}" class="form-control mb-2" required>
                <input name="email" value="{{ $admin->email }}" class="form-control mb-2" required>
                <select name="role" class="form-select mb-2">
                    <option value="admin" {{ $admin->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="superadmin" {{ $admin->role === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle confirmation for Delete
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            const name = this.dataset.name;
            Swal.fire({
                title: 'Are you sure?',
                text: `Delete admin "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Success Alert
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: @json(session('success')),
        confirmButtonColor: '#3085d6'
    });
    @endif

    // Error Alert
    @if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json($errors->first()),
        confirmButtonColor: '#d33'
    });
    @endif

    // Prevent Enter key submission in modals
    document.querySelectorAll('.modal form').forEach(form => {
        form.addEventListener('keypress', e => {
            if (e.key === 'Enter') e.preventDefault();
        });
    });
});
</script>
@endpush
