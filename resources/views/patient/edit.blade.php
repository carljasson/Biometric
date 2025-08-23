<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - Biometric Medical Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Back</a>
    </div>

    <h3 class="mb-4">Edit Your Information</h3>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('update.profile') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">First Name</label>
                <input type="text" name="firstname" value="{{ old('firstname', $patient->firstname) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Middle Name</label>
                <input type="text" name="middlename" value="{{ old('middlename', $patient->middlename) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Last Name</label>
                <input type="text" name="lastname" value="{{ old('lastname', $patient->lastname) }}" class="form-control">
            </div>

            <!-- Gender -->
            <div class="col-md-4">
                <label class="form-label">Gender <span class="text-danger">*</span></label>
                <select name="gender" class="form-select" required>
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $patient->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $patient->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <!-- Birthday & Age -->
            <div class="col-md-4">
                <label class="form-label">Birthday</label>
                <input type="date" name="birthday" id="birthday" value="{{ old('birthday', $patient->birthday) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Age <span class="text-danger">*</span></label>
                <input type="number" name="age" id="age" value="{{ old('age', $patient->age) }}" class="form-control" readonly>
            </div>

            <!-- Status -->
            <div class="col-md-4">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="">Select Status</option>
                    <option value="Single" {{ old('status', $patient->status) == 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Married" {{ old('status', $patient->status) == 'Married' ? 'selected' : '' }}>Married</option>
                    <option value="Divorced" {{ old('status', $patient->status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    <option value="Widowed" {{ old('status', $patient->status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Address</label>
                <input type="text" name="address" value="{{ old('address', $patient->address) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $patient->email) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact Name</label>
                <input type="text" name="contact_name" value="{{ old('contact_name', $patient->contact_name) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $patient->contact_number) }}" class="form-control">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary w-100">💾 Save Changes</button>
        </div>
    </form>
</div>

<!-- JavaScript to auto-calculate age -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const birthdayInput = document.getElementById('birthday');
        const ageInput = document.getElementById('age');

        function calculateAge(dateString) {
            const today = new Date();
            const birthDate = new Date(dateString);
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }

        birthdayInput.addEventListener('change', function () {
            const age = calculateAge(this.value);
            if (!isNaN(age)) {
                ageInput.value = age;
            }
        });

        // Trigger on load if birthday exists
        if (birthdayInput.value) {
            const age = calculateAge(birthdayInput.value);
            if (!isNaN(age)) {
                ageInput.value = age;
            }
        }
    });
</script>

</body>
</html>
