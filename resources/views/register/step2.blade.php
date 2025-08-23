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

        .fingerprint-box:hover {
            transform: scale(1.05);
        }

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

        button:hover {
            background-color: #ffffff33;
        }

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
        <div class="fingerprint-box" onclick="startFingerprintScan()">
            <img src="https://cdn-icons-png.flaticon.com/512/483/483361.png" class="fingerprint-icon" alt="Fingerprint Icon">
        </div>
    </form>

    <button type="button" onclick="skipStep()">Skip Fingerprint</button>
    <p class="note">Fingerprint is optional. You can skip and still complete registration.</p>
</div>

<script>
function skipStep() {
    window.location.href = "{{ url('/register/step3') }}";
}

async function startFingerprintScan() {
    if (!window.PublicKeyCredential || !PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable) {
        console.warn("WebAuthn not supported or not available. Using fallback.");
        return fallbackFingerprint();
    }

    const isAvailable = await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
    if (!isAvailable) {
        console.warn("Platform authenticator not available.");
        Swal.fire({
            icon: 'error',
            title: 'Not Supported',
            text: 'This device/browser does not support fingerprint scanning.',
        });
        return fallbackFingerprint();
    }

    try {
        const challenge = new Uint8Array(32);
        window.crypto.getRandomValues(challenge);

        const publicKey = {
            challenge,
            rp: { name: "Biometric Medical Access" },
            user: {
                id: Uint8Array.from("1234567890", c => c.charCodeAt(0)),
                name: "user@example.com",
                displayName: "User"
            },
            pubKeyCredParams: [{ type: "public-key", alg: -7 }],
            authenticatorSelection: {
                authenticatorAttachment: "platform",
                userVerification: "required"
            },
            timeout: 60000,
            attestation: "none"
        };

        const credential = await navigator.credentials.create({ publicKey });

        if (!credential) throw new Error("No credential returned");

        const encoded = btoa(JSON.stringify(credential));
        document.getElementById("fingerprint_data").value = encoded;

        Swal.fire({
            icon: 'success',
            title: 'Fingerprint scan successful!',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            document.getElementById("fingerprintForm").submit();
        });

    } catch (error) {
        console.warn("Fingerprint scan failed:", error);
        Swal.fire({
            icon: 'warning',
            title: 'Scan Failed',
            text: 'Falling back to simulated fingerprint.',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            fallbackFingerprint();
        });
    }
}

function fallbackFingerprint() {
    const simulated = "simulated-" + Date.now();
    document.getElementById("fingerprint_data").value = btoa(simulated);

    Swal.fire({
        icon: 'info',
        title: 'Simulated Fingerprint',
        text: 'Real scanner not available. Using fallback fingerprint.',
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        document.getElementById("fingerprintForm").submit();
    });
}
</script>
