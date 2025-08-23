@extends('layouts.patients')
@section('content')
<div class="container mt-5 text-white">
    <h3>Patient Info</h3>
    <ul class="list-group">
        <li class="list-group-item">Name: {{ $user->name }}</li>
        <li class="list-group-item">Email: {{ $user->email }}</li>
        <li class="list-group-item">Contact: {{ $user->contact_number }}</li>
        <li class="list-group-item">Address: {{ $user->address }}</li>
        <!-- Add more fields as needed -->
    </ul>
</div>
@endsection
