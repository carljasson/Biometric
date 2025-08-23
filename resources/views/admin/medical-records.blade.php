@extends('layouts.app')
@section('title', 'All Medical Records')

@php use Illuminate\Support\Facades\Auth; @endphp

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
        <div class="record-header mb-3">
            <h3 class="mb-0"><i class="fas fa-file-medical"></i> All Medical Records</h3>
        </div>

        @if($patients->isEmpty())
            <div class="alert alert-warning m-3">
                <i class="fas fa-info-circle"></i> No medical records found.
            </div>
        @else
            <div class="table-responsive p-3">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th><i class="fas fa-user"></i> Name</th>
                            <th><i class="fas fa-tint"></i> Blood Type</th>
                            <th><i class="fas fa-allergies"></i> Allergies</th>
                            <th><i class="fas fa-pills"></i> Medications</th>
                            <th><i class="fas fa-cut"></i> Surgeries</th>
                            <th><i class="fas fa-notes-medical"></i> History</th>
                            <th><i class="fas fa-syringe"></i> Dose</th>
                            <th><i class="fas fa-eye"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr>
                            <td>{{ optional($patient->user)->name ?? 'Unknown' }}</td>
                            <td>{{ $patient->blood_type ?? 'N/A' }}</td>
                            <td>{{ $patient->allergies ?? 'N/A' }}</td>
                            <td>{{ $patient->medications ?? 'N/A' }}</td>
                            <td>{{ $patient->surgeries ?? 'N/A' }}</td>
                            <td>{{ $patient->medical_history ?? 'N/A' }}</td>
                            <td>{{ $patient->dose ?? 'N/A' }}</td>
                            <td class="text-center">
                                <a href="javascript:void(0)"
                                   class="btn btn-info btn-sm viewRecordBtn"
                                   data-bs-toggle="modal"
                                   data-bs-target="#viewRecordModal"
                                   data-name="{{ optional($patient->user)->name ?? 'Unknown' }}"
                                   data-blood_type="{{ $patient->blood_type ?? 'N/A' }}"
                                   data-allergies="{{ $patient->allergies ?? 'N/A' }}"
                                   data-medications="{{ $patient->medications ?? 'N/A' }}"
                                   data-surgeries="{{ $patient->surgeries ?? 'N/A' }}"
                                   data-history="{{ $patient->medical_history ?? 'N/A' }}"
                                   data-dose="{{ $patient->dose ?? 'N/A' }}">
                                   <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center p-3">
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>

<!-- View Record Modal -->
<div class="modal fade" id="viewRecordModal" tabindex="-1" aria-labelledby="viewRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewRecordModalLabel">Medical Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="recordName"></span></p>
                <p><strong>Blood Type:</strong> <span id="recordBloodType"></span></p>
                <p><strong>Allergies:</strong> <span id="recordAllergies"></span></p>
                <p><strong>Medications:</strong> <span id="recordMedications"></span></p>
                <p><strong>Surgeries:</strong> <span id="recordSurgeries"></span></p>
                <p><strong>Medical History:</strong> <span id="recordHistory"></span></p>
                <p><strong>Dose:</strong> <span id="recordDose"></span></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert for success
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: @json(session('success')),
        confirmButtonColor: '#3085d6'
    });
    @endif

    // SweetAlert for errors
    @if($errors->any())
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json($errors->first()),
        confirmButtonColor: '#d33'
    });
    @endif

    // Populate Modal on View Click
    document.querySelectorAll('.viewRecordBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('recordName').textContent = this.dataset.name;
            document.getElementById('recordBloodType').textContent = this.dataset.blood_type;
            document.getElementById('recordAllergies').textContent = this.dataset.allergies;
            document.getElementById('recordMedications').textContent = this.dataset.medications;
            document.getElementById('recordSurgeries').textContent = this.dataset.surgeries;
            document.getElementById('recordHistory').textContent = this.dataset.history;
            document.getElementById('recordDose').textContent = this.dataset.dose;
        });
    });
});
</script>
@endpush
