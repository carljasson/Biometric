@extends('layouts.app')
@section('title', 'Admitted Patients')

@section('content')
<div class="container mt-4">
    <div class="record-header mb-3">
        <!-- Card Header -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-hospital-fill me-2"></i> Admitted Patients</h5>
                <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Add Patient
                </button>
            </div>
            <!-- Search Bar -->
            <div class="p-3"  style="max-width: 300px;">
                <input type="text" class="form-control" id="searchPatients" placeholder="Search patients...">
            </div>

            <!-- Patient Table -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Condition</th>
                            <th>Admit Date</th>
                            <th>Room No</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patientsTable">
                        @foreach($patients as $patient)
                        <tr id="row-{{ $patient->id }}">
                            <form method="POST" action="{{ route('patients.update', $patient->id) }}" id="updateForm-{{ $patient->id }}">
                                @csrf
                                @method('PUT')
                                <td><input type="text" name="name" class="form-control text-center" value="{{ $patient->name }}" readonly></td>
                                <td><input type="number" name="age" class="form-control text-center" value="{{ $patient->age }}" readonly></td>
                                <td>
                                    <select name="gender" class="form-select text-center" disabled>
                                        <option value="Male" {{ $patient->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $patient->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="condition" class="form-select text-center" disabled>
                                        <option value="Stable" {{ $patient->condition == 'Stable' ? 'selected' : '' }}>Stable</option>
                                        <option value="Critical" {{ $patient->condition == 'Critical' ? 'selected' : '' }}>Critical</option>
                                        <option value="Recovering" {{ $patient->condition == 'Recovering' ? 'selected' : '' }}>Recovering</option>
                                        <option value="Observation" {{ $patient->condition == 'Observation' ? 'selected' : '' }}>Observation</option>
                                    </select>
                                </td>
                                <td><input type="date" name="admit_date" class="form-control text-center" value="{{ $patient->admit_date }}" readonly></td>
                                <td><input type="text" name="room_number" class="form-control text-center" value="{{ $patient->room_number }}" readonly></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        <button type="button" class="btn btn-warning btn-sm" onclick="enableEdit({{ $patient->id }})"><i class="bi bi-pencil-square"></i></button>
                                        <button type="submit" class="btn btn-success btn-sm d-none" id="saveBtn-{{ $patient->id }}"><i class="bi bi-check-lg"></i></button>
                                        <button type="button" class="btn btn-secondary btn-sm d-none" onclick="cancelEdit()" id="cancelBtn-{{ $patient->id }}"><i class="bi bi-x-lg"></i></button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deletePatient({{ $patient->id }})"><i class="bi bi-trash-fill"></i></button>
                                        <button type="button" class="btn btn-info btn-sm" onclick="printPatient('{{ $patient->id }}')"><i class="bi bi-printer-fill"></i></button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                        <form method="POST" action="{{ route('patients.destroy', $patient->id) }}" id="deleteForm-{{ $patient->id }}" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Patient Modal -->
<div class="modal fade" id="addPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('patients.store') }}" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i> Add New Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                @foreach([['name', 'text', 'Patient Name'], ['age', 'number', 'Age'], ['admit_date', 'date', 'Admit Date'], ['room_number', 'text', 'Room No']] as [$name, $type, $label])
                <div class="mb-3">
                    <label class="form-label">{{ $label }}</label>
                    <input name="{{ $name }}" type="{{ $type }}" class="form-control" required>
                </div>
                @endforeach
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Condition</label>
                    <select name="condition" class="form-select" required>
                        <option value="" disabled selected>Select Condition</option>
                        <option>Stable</option>
                        <option>Critical</option>
                        <option>Recovering</option>
                        <option>Observation</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Save Patient</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success') || session('add_success') || session('delete_success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: "{{ session('success') ?? session('add_success') ?? session('delete_success') }}",
    confirmButtonColor: '#3085d6',
});
</script>
@endif

@if($errors->any())
<script>
Swal.fire({
    icon: 'error',
    title: 'Validation Error',
    text: "{{ $errors->first() }}",
    confirmButtonColor: '#d33',
});
</script>
@endif

<script>
function enableEdit(id) {
    const row = document.getElementById(`row-${id}`);
    row.querySelectorAll('input, select').forEach(e => {
        e.removeAttribute('readonly');
        e.removeAttribute('disabled');
    });
    document.getElementById(`saveBtn-${id}`).classList.remove('d-none');
    document.getElementById(`cancelBtn-${id}`).classList.remove('d-none');
}

function cancelEdit() {
    location.reload();
}

function deletePatient(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the patient.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`deleteForm-${id}`).submit();
        }
    });
}

function printPatient(id) {
    const row = document.getElementById(`row-${id}`);
    const inputs = row.querySelectorAll('input, select');
    let html = `<html><head><title>Patient Info</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body><div class="container mt-4"><h3 class="text-center">Patient Record</h3><table class="table table-bordered"><tbody>`;
    inputs.forEach(input => {
        const label = input.name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        const value = input.value || input.options?.[input.selectedIndex]?.text;
        html += `<tr><th>${label}</th><td>${value}</td></tr>`;
    });
    html += `</tbody></table></div></body></html>`;
    const printWin = window.open('', '_blank');
    printWin.document.write(html);
    printWin.document.close();
    printWin.print();
}
</script>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('searchPatients').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('#patientsTable tr');

        rows.forEach(row => {
            const name = row.querySelector('input[name="name"]')?.value.toLowerCase() || '';
            const age = row.querySelector('input[name="age"]')?.value.toLowerCase() || '';
            const gender = row.querySelector('select[name="gender"]')?.value.toLowerCase() || '';
            const condition = row.querySelector('select[name="condition"]')?.value.toLowerCase() || '';

            const combined = `${name} ${age} ${gender} ${condition}`;
            row.style.display = combined.includes(query) ? '' : 'none';
        });
    });
});
</script>
@endpush
