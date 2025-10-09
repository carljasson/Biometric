<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Launching Fingerprint App...</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // Try to open the fingerprint app immediately
        window.onload = function() {
            window.location.href = "{{ $deeplink }}";
            // Fallback to step 2 if app not installed
            setTimeout(() => {
                window.location.href = "{{ route('register.step2') }}";
            }, 3000);
        }
    </script>
</head>
<body class="text-center mt-5">
    <h3>Launching your fingerprint app...</h3>
    <p>If nothing happens, <a href="{{ $deeplink }}">tap here</a> to open manually.</p>
</body>
</html>
