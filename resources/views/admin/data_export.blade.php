@extends('layouts.app')

@section('content')

<div id="mainContent" class="p-4">
    <h2 class="mb-4">🩺 Medical Record Access & Export</h2>

    {{-- Export + Search --}}
    <div class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-stretch gap-2 flex-wrap">
        <div class="d-flex gap-2">
            <a href="{{ route('export.excel') }}" class="btn btn-success btn-sm" data-bs-toggle="tooltip" title="Download as Excel">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Export to Excel
            </a>
            <a href="{{ route('export.pdf') }}" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Download as PDF">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export to PDF
            </a>
            <a href="#" class="btn btn-secondary btn-sm" onclick="window.print()" data-bs-toggle="tooltip" title="Print this page">
                <i class="bi bi-printer-fill me-1"></i> Print
            </a>
        </div>

        <form method="GET" action="{{ route('admin.records') }}" class="d-flex align-items-center">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search patients..." value="{{ request('search') }}">
            <button class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i></button>
        </form>
    </div>

    {{-- Patient Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Condition</th>
                    <th>Room Number</th>
                    <th>Admit Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>{{ $loop->iteration + ($patients->currentPage() - 1) * $patients->perPage() }}</td>
                        <td>{{ $patient->name }}</td>
                        <td>{{ $patient->age ?? 'N/A' }}</td>
                        <td>{{ ucfirst($patient->gender ?? 'N/A') }}</td>
                        <td>{{ $patient->condition ?? 'N/A' }}</td>
                        <td>{{ $patient->room_number ?? 'N/A' }}</td>
                        <td>{{ $patient->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No patient records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $patients->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('sidebarToggle');

        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }

        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed'));
        });

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
