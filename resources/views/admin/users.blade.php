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
        <div class="bg-white shadow rounded p-4 flex flex-col justify-between hover:shadow-lg transition">
            <div>
                <h5 class="font-semibold text-lg">{{ $user->firstname }} {{ $user->lastname }}</h5>
                <p class="text-sm text-gray-600"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="text-sm text-gray-600"><strong>Phone:</strong> {{ $user->phone }}</p>
                <p class="text-sm text-gray-600"><strong>Age:</strong> {{ $user->age }}</p>
                <p class="text-sm text-gray-600"><strong>Birthday:</strong> {{ $user->birthday }}</p>

                <div class="mt-2">
                    <p class="text-sm font-semibold text-gray-700">Contact Info</p>
                    <p class="text-sm text-gray-600"><strong>Address:</strong> {{ $user->address }}</p>
                    <p class="text-sm text-gray-600"><strong>Emergency:</strong> {{ $user->contact_name }} ({{ $user->contact_number }})</p>
                </div>

                <div class="mt-2">
                    <p class="text-sm font-semibold text-gray-700">Biometric Data</p>
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
