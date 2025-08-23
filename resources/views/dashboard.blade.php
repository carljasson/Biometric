<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Biometric Medical Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
        }
        .bottom-nav a {
            color: #333;
            text-align: center;
            font-size: 14px;
            text-decoration: none;
        }
        .bottom-nav a i {
            font-size: 20px;
            display: block;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4 mb-5">

    @if(session('showProfileModal'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('profileModal')).show();
            });
        </script>
    @endif

    <!-- Dashboard Card -->
    <div class="card shadow mt-3">
        <div class="card-header bg-primary text-white">
            <h4>Welcome, {{ $patient->firstname }}!</h4>

            @if($announcements && $announcements->count())
                <div class="mt-3">
                    @foreach($announcements as $announcement)
                        <div class="alert alert-info bg-white text-dark mb-2">
                            <strong>📢 {{ $announcement->created_at->format('F j, Y h:i A') }}</strong><br>
                            {{ $announcement->message }}
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary mt-3 bg-white text-dark">
                    No announcements yet.
                </div>
            @endif
        </div>

        <div class="card-body">
            <div class="alert alert-danger">
                <h5>🚨 Emergency Step-by-Step Guide</h5>
                <ol class="mb-0">
                    <li>Stay calm and assess the situation.</li>
                    <li>Call emergency services or notify nearby people.</li>
                    <li>Use this app to scan the patient's fingerprint or face to access their medical info.</li>
                    <li>Follow the emergency response instructions provided in their profile.</li>
                    <li>Keep the patient stable while waiting for help.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <a href="#"><i class="fas fa-home"></i><br>Home</a>
    <a href="#" data-bs-toggle="modal" data-bs-target="#profileModal"><i class="fas fa-user-circle"></i><br>My Profile</a>
    <a href="{{ route('emergency') }}"><i class="fas fa-phone-alt text-danger"></i><br>Emergency</a>
    <a href="{{ route('scan.page') }}"><i class="fas fa-fingerprint"></i><br>Scan</a>
</div>

<!-- Personal Info Modal -->
<div class="modal fade" id="personalInfoModal" tabindex="-1" aria-labelledby="personalInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="personalInfoModalLabel">Personal Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>Full Name:</strong> {{ $patient->firstname }} {{ $patient->lastname }}</li>
            <li class="list-group-item"><strong>Birthday:</strong> {{ $patient->birthday }}</li>
            <li class="list-group-item"><strong>Age:</strong> {{ $patient->age }}</li>
            <li class="list-group-item"><strong>Address:</strong> {{ $patient->address }}</li>
            <li class="list-group-item"><strong>Email:</strong> {{ $patient->email }}</li>
            <li class="list-group-item"><strong>Phone Number:</strong> {{ $patient->phone }}</li>
            <li class="list-group-item"><strong>Contact Person Name:</strong> {{ $patient->contact_name }}</li>
            <li class="list-group-item"><strong>Contact Person Number:</strong> {{ $patient->contact_number }}</li>
        </ul>
        <a href="{{ route('edit.profile') }}" class="btn btn-warning w-100">
            <i class="fas fa-edit"></i> Edit Information
        </a>
      </div>
    </div>
  </div>
</div>

<!-- My Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="profileModalLabel">My Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @php
            $hasMedicalRecord = $patient->medicalRecord;
            $medicalRoute = $hasMedicalRecord
                ? route('patient.medical_records.show', $hasMedicalRecord->id)
                : route('patient.medical_records.create');
        @endphp
        <ul class="list-group">
            <li class="list-group-item" data-bs-toggle="modal" data-bs-target="#personalInfoModal" data-bs-dismiss="modal" style="cursor: pointer;">
                <i class="fas fa-user"></i> Personal Info
            </li>
            <li class="list-group-item" style="cursor: pointer;" onclick="window.location.href='{{ $medicalRoute }}'">
                <i class="fas fa-notes-medical"></i> My Medical Records
            </li>
            <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="window.location.href='{{ route('about') }}'">
                <i class="fas fa-info-circle"></i> About This App
            </li>
            <li class="list-group-item list-group-item-action" style="cursor: pointer;" onclick="toggleDarkMode()">
                <i class="fas fa-moon"></i> Toggle Dark Mode
            </li>
            <form action="{{ route('logout') }}" method="POST" class="list-group-item p-0 m-0">
                @csrf
                <button class="btn w-100 text-start" style="cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Auto-dismiss success alert -->
<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert-dismissible');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            alert.remove();
        }
    }, 3000);

    function toggleDarkMode() {
        document.body.classList.toggle('bg-dark');
        document.body.classList.toggle('text-light');
    }
</script>

</body>
</html>
