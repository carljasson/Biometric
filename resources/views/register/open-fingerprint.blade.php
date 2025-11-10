<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fingerprint Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        // Pass PHP variable to JS
        const userId = {{ $userId }};
        
        document.addEventListener('DOMContentLoaded', function() {
            // Automatically open your fingerprint app
            window.location.href = `myfingerprint://scan?userId=${userId}`;

            setTimeout(() => {
                alert("If your fingerprint app didn’t open automatically, tap the button below.");
            }, 3000);
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

    <!-- Next Button (goes to step3) -->
   <a href="{{ route('register.step3', ['user' => $userId]) }}"
   style="display:inline-block; padding:10px 20px; background:#0d6efd; color:white; border-radius:8px; text-decoration:none;">
   Next
</a>



</body>
</html>
