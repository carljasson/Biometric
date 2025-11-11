@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div id="mainContent" class="p-4">

    <h4 class="mb-4"><i class="bi bi-people-fill me-2"></i> Registered Users</h4>

    <!-- Search -->
    <div class="mb-3 d-flex" style="max-width: 300px;">
        <input type="text" class="form-control me-2" id="searchUsers" placeholder="Search users...">
    </div>

    <!-- Users Cards -->
    <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach($users as $user)
        <div class="rounded shadow-md overflow-hidden transition hover:shadow-lg">

            <!-- Personal Info -->
            <div class="bg-blue-100 text-gray-800 p-3">
                <h5 class="font-semibold text-lg">{{ $user->firstname }} {{ $user->lastname }}</h5>
                <p class="text-sm"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="text-sm"><strong>Phone:</strong> {{ $user->phone }}</p>
                <p class="text-sm"><strong>Age:</strong> {{ $user->age }}</p>
                <p class="text-sm"><strong>Birthday:</strong> {{ $user->birthday }}</p>
            </div>

            <!-- Contact Info -->
            <div class="bg-green-100 text-gray-800 p-3">
                <p class="text-sm"><strong>Address:</strong> {{ $user->address }}</p>
                <p class="text-sm"><strong>Emergency Contact:</strong> {{ $user->contact_name }} ({{ $user->contact_number }})</p>
            </div>

            <!-- Biometric Data -->
            <div class="bg-yellow-100 text-gray-800 p-3">
                <p class="text-sm">
                    Fingerprint: 
                    @if($user->fingerprint_data)
                        <span class="badge bg-success">Captured</span>
                    @else
                        <span class="badge bg-secondary">None</span>
                    @endif
                </p>
                <p class="text-sm">
                    Face Scan: 
                    @if($user->face_scan_path)
                        <a href="{{ asset('storage/' . $user->face_scan_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                    @else
                        <span class="badge bg-secondary">N/A</span>
                    @endif
                </p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll('#mainContent > div.grid > div');

    document.getElementById('searchUsers').addEventListener('input', function () {
        const query = this.value.toLowerCase();

        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(query) ? 'flex' : 'none';
        });
    });
});
</script>
@endpush
