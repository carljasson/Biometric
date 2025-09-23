<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 2 - Fingerprint (Optional)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003049, #1d3557);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }

        .form-container {
            background: #ffffff10;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .fingerprint-box {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            margin: 20px auto;
            background: #00b4d8;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
            box-shadow: 0 0 25px #00b4d8;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .fingerprint-box:hover { transform: scale(1.05); }

        .fingerprint-icon {
            width: 70px;
            filter: brightness(0) invert(1);
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 25px #00b4d8; }
            50% { box-shadow: 0 0 50px #00e0ff; }
        }

        button {
            background-color: #ffffff22;
            color: white;
            padding: 12px;
            width: 100%;
            border: 2px solid #fff;
            border-radius: 10px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover { background-color: #ffffff33; }

        .note {
            font-size: 12px;
            margin-top: 10px;
            color: #ccc;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Step 2: Fingerprint Scan</h2>
    <p>Tap the fingerprint icon to scan</p>

    <form method="POST" action="{{ url('/register/step2') }}" id="fingerprintForm">
        @csrf
        <input type="hidden" name="fingerprint_data" id="fingerprint_data">

        <!-- Fingerprint trigger -->
<a href="#" id="launchApp">
    <div class="fingerprint-box">
        <img src="https://cdn-icons-png.flaticon.com/512/483/483361.png"
             class="fingerprint-icon" alt="Fingerprint Icon">
    </div>
</a>

<script>
document.getElementById("launchApp").addEventListener("click", function(e) {
    e.preventDefault();
    // Launch C# app with the user_id
   
});
</script>

    </form>

    <button type="button" onclick="skipStep()">Skip Fingerprint</button>
    <p class="note">Fingerprint is optional. You can skip and still complete registration.</p>
</div>

<script>
document.getElementById("launchApp").addEventListener("click", function(e) {
    e.preventDefault();
    // Launch custom protocol
    window.location.href = "myfingerprint://start";
});

function skipStep() {
    window.location.href = "{{ url('/register/step3') }}";
}
</script>

</body>
</html>
