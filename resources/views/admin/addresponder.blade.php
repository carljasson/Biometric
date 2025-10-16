@extends('layouts.app')
@section('title', 'Responder Management')

@section('content')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .record-header {
        background-color: #198754;
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
            <h3 class="mb-0"><i class="bi bi-people-fill"></i> Manage Responders</h3>
            <button class="btn btn-light text-dark" data-bs-toggle="modal" data-bs-target="#addResponderModal">
                <i class="bi bi-person-plus-fill"></i> Add Responder
            </button>
        </div>

        @if($responders->isEmpty())
            <div class="alert alert-warning m-3">
                <i class="bi bi-info-circle-fill"></i> No responders found.
            </div>
        @else
            <div class="table-responsive p-3">
                <table class="table table-hover table-bordered">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responders as $responder)
                        <tr>
                            <td>{{ $responder->name }}</td>
                            <td>{{ $responder->email }}</td>
                            <td>{{ $responder->location }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editResponderModal{{ $responder->id }}">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('responders.destroy', $responder->id) }}" class="d-inline delete-responder-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-name="{{ $responder->name }}">
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

<!-- Add Responder Modal -->
<div class="modal fade" id="addResponderModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('responders.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Responder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input name="name" class="form-control mb-2" placeholder="Full Name" required>
                <input name="email" type="email" class="form-control mb-2" placeholder="Email" required>
                <input name="location" class="form-control mb-2" placeholder="Location" required>
                <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Add Responder</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modals -->
@foreach($responders as $responder)
<div class="modal fade" id="editResponderModal{{ $responder->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('responders.update', $responder->id) }}" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Responder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input name="name" value="{{ $responder->name }}" class="form-control mb-2" required>
                <input name="email" value="{{ $responder->email }}" class="form-control mb-2" required>
                <input name="location" value="{{ $responder->location }}" class="form-control mb-2" required>
                <input name="password" type="password" class="form-control mb-2" placeholder="New Password (optional)">
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