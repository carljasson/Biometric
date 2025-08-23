<!-- resources/views/user/aboutthisapp.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About This App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('dashboard') }}" class="btn btn-primary">← Back </a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">📱 About This App</h4>
        </div>
        <div class="card-body">
            <p>
                <strong>Biometric Medical Access</strong> is a secure and user-friendly healthcare application designed to streamline medical access for patients using modern biometric technology.
            </p>
            <p>
                It allows users to register and log in using fingerprint and facial recognition, helping ensure that only authorized individuals can view or update their medical records. Patients can manage their personal information, access emergency contacts, and securely store important health data.
            </p>
            <p>
                The system is designed with both <strong>security</strong> and <strong>ease of use</strong> in mind, aiming to make emergency medical assistance faster and more reliable.
            </p>

        </div>
    </div>

</div>

</body>
</html>
