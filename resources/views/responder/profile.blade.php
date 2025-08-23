@extends('layouts.responder')

@section('title', 'Responder Profile')

@section('content')
<div class="container mt-4 mb-5">
    <!-- Back Button -->
    <a href="{{ route('responder.dashboard') }}" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">👤 My Profile</h4>
        </div>

        <div class="card-body text-dark">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Full Name:</strong> {{ $responder->name }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $responder->email }}</li>
                <li class="list-group-item"><strong>Responder ID:</strong> {{ $responder->id }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
