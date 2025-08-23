@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3 class="text-success">Match Found</h3>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Phone:</strong> {{ $user->phone }}</p>
    <p><strong>Address:</strong> {{ $user->address }}</p>
    <p><strong>Emergency Contact:</strong> {{ $user->contact_name }} ({{ $user->contact_number }})</p>
</div>
@endsection
