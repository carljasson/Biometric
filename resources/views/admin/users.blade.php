@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div id="mainContent" class="p-4">

    <h4 class="mb-4"><i class="bi bi-people-fill me-2"></i> Registered Users</h4>

    <!-- Search -->
    <div class="mb-3 d-flex" style="max-width: 300px;">
        <input type="text" class="form-control me-2" id="searchUsers" placeholder="Search users...">
    </div>

    <!-- Users Table -->
    <div class="overflow-auto">
        <table class="table table-bordered table-hover align-middle" style="table-layout: auto; min-width: 100%;">
            <thead class="table-light text-center">
                <tr>
                    <th colspan="7" class="bg-primary text-white">🧑 Personal Info</th>
                    <th colspan="2" class="bg-success text-white">📞 Contact Info</th>
                    <th colspan="2" class="bg-info text-white">👨‍👩‍👧 Emergency Contact</th>
                    <th colspan="2" class="bg-warning text-dark">🔐 Biometric Data</th>
                </tr>
                <tr class="bg-secondary text-white">
                    <th>Firstname</th>
                    <th>Middlename</th>
                    <th>Lastname</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Birthday</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Contact Name</th>
                    <th>Contact Number</th>
                    <th>Fingerprint</th>
                    <th>Face Scan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td style="white-space: normal; word-break: break-word;">{{ $user->firstname }}</td>
                    <td style="white-space: normal; word-break: break-word;">{{ $user->middlename }}</td>
                    <td style="white-space: normal; word-break: break-word;">{{ $user->lastname }}</td>
                    <td style="white-space: normal; word-break: break-word;">{{ $user->email }}</td>
                    <td><i class="fas fa-lock text-muted"></i> Hidden</td>
                    <td>{{ $user->birthday }}</td>
                    <td>{{ $user->age }}</td>
                    <td style="white-space: normal; word-break: break-word;">{{ $user->phone }}</td>
                    <td style="white-space: normal; word-break: break-word;">{{ $user->address }}</td>
                    <td style="white-space: normal; word-break: break-word;">{{ $user->contact_name }}</td>
                    <td style="white-space: normal; word-break: break-word;">{{ $user->contact_number }}</td>
                    <td>
                        @if($user->fingerprint_data)
                            <span class="badge bg-success">Captured</span>
                        @else
                            <span class="badge bg-secondary">None</span>
                        @endif
                    </td>
                    <td>
                        @if($user->face_scan_path)
                            <a href="{{ asset('storage/' . $user->face_scan_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        @else
                            <span class="badge bg-secondary">N/A</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('searchUsers').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
});
</script>
@endpush
