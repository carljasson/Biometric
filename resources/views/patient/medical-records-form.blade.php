@extends('layouts.patients')

@section('content')
<div class="container py-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card shadow-sm p-4">
        <h4 class="mb-4 text-primary"><i class="bi bi-clipboard-pulse"></i> Patient Medical Information</h4>

        <form method="POST" action="{{ route('patient.medical_records.store') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $patient->id }}">

            <!-- Blood Information -->
            <h6 class="text-secondary">🩸 Blood Information</h6>
            <div class="mb-3">
                <label class="form-label">Blood Type <span class="text-danger">*</span></label>
                <select class="form-select" name="blood_type" id="blood_type" required>
                    <option value="">Select Blood Type</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <!-- Allergies -->
            <h6 class="text-secondary mt-4">🌿 Allergies</h6>
            <div class="mb-3">
                <label class="form-label">Select Known Allergies</label>
                <select class="form-select" name="allergies[]" id="allergies" multiple>
                    <option value="Peanuts">Peanuts</option>
                    <option value="Shellfish">Shellfish</option>
                    <option value="Eggs">Eggs</option>
                    <option value="Milk">Milk</option>
                    <option value="Wheat">Wheat</option>
                    <option value="Soy">Soy</option>
                    <option value="Fish">Fish</option>
                    <option value="Tree Nuts">Tree Nuts</option>
                    <option value="Pollen">Pollen</option>
                    <option value="Dust">Dust</option>
                    <option value="Pet Dander">Pet Dander</option>
                    <option value="Insect Stings">Insect Stings</option>
                    <option value="Latex">Latex</option>
                    <option value="Penicillin">Penicillin</option>
                    <option value="Other">Other</option>
                </select>
                <small class="text-muted">Hold Ctrl (or tap) to select multiple allergies.</small>
            </div>

            <!-- Medication Details -->
            <h6 class="text-secondary mt-4">💊 Medications</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Current Medications</label>
                    <input type="text" class="form-control" name="medications" placeholder="e.g. Paracetamol, Amoxicillin">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dose</label>
                    <input type="text" class="form-control" name="dose" placeholder="e.g. 500mg">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Reason</label>
                    <input type="text" class="form-control" name="reason_medication" placeholder="e.g. Fever">
                </div>
            </div>

            <!-- Surgery Info -->
            <h6 class="text-secondary mt-4">🛠️ Surgical History</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Type of Surgery</label>
                    <input type="text" class="form-control" name="surgery" placeholder="e.g. Appendectomy">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <input type="text" class="form-control" name="year" placeholder="e.g. 2021">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Complications</label>
                    <input type="text" class="form-control" name="Complications" placeholder="e.g. Infection">
                </div>
            </div>

            <!-- Medical History -->
            <h6 class="text-secondary mt-4">📋 Past Medical History</h6>
            <div class="mb-3">
                <label class="form-label">Summary of Medical Conditions</label>
                <textarea class="form-control" name="medical_history" rows="3" placeholder="e.g. Asthma, Diabetes, Hypertension..."></textarea>
            </div>

            <!-- Submit -->
            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success w-100 w-md-auto">
                    <i class="bi bi-save"></i> Save Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        min-height: 42px;
        padding: 8px 12px;
        font-size: 1rem;
        border: 1px solid #ced4da;
    }

    .select2-selection__choice {
        background-color: #0d6efd !important;
        color: white !important;
        border: none !important;
    }

    @media (max-width: 576px) {
        .form-label, .btn, .form-control, .select2-selection {
            font-size: 0.95rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('#blood_type').select2({
            placeholder: "Select blood type",
            dropdownAutoWidth: true,
            width: 'resolve'
        });

        $('#allergies').select2({
            placeholder: "Select allergies",
            dropdownAutoWidth: true,
            width: 'resolve'
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Please review the form',
                text: 'Some fields are invalid or incomplete.',
                confirmButtonColor: '#d33'
            });
        @endif
    });
</script>
@endpush
