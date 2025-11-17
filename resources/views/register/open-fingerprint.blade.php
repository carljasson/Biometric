<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fingerprint Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Pass PHP variable to JS
        const userId = {{ $userId }};
        
        document.addEventListener('DOMContentLoaded', function() {
            // Automatically open your fingerprint app
            window.location.href = `myfingerprint://scan?userId=${userId}`;

            setTimeout(() => {
                alert("If your fingerprint app didn’t open automatically, tap the button below.");
            }, 3000);

            // Add click listener to Done button
            const doneBtn = document.getElementById('doneBtn');
            doneBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default link action

                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful!',
                    text: 'You have successfully registered your fingerprint.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Redirect to step1
                    window.location.href = "{{ route('register.step1') }}";
                });
            });
        });
    </script>
</head>
<body style="text-align:center; margin-top:80px; font-family:sans-serif;">

    <h2>Fingerprint Setup</h2>
    <p>Please scan your fingerprint to complete your registration.</p>

    <!-- Launch Fingerprint App -->
    <a href="myfingerprint://scan?userId={{ $userId }}"
       style="display:inline-block; padding:10px 20px; background:#198754; color:white; border-radius:8px; text-decoration:none; margin-bottom:10px;">
       Launch Fingerprint App
    </a>
    <br><br>

    <!-- Done Button -->
    <a href="#"
       id="doneBtn"
       style="display:inline-block; padding:10px 20px; background:#0d6efd; color:white; border-radius:8px; text-decoration:none;">
       Done
    </a>

</body>
</html>
