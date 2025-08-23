<!DOCTYPE html>
<html>

<head>
    <title>Biometric Medical Access</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Existing styles */
        body {
            background-image: url('{{ asset('images/background.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        }

        .top-left-logo {
            position: absolute;
            top: 5px;
            left: 20px;
        }

        .top-left-logo img {
            max-width: 180px;
            width: 100%;
            height: auto;
        }

        .top-right-icons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .top-right-icons a,
        .top-right-icons .dropdown-toggle {
            color: white;
            font-size: 1.5rem;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .top-right-icons a:hover,
        .top-right-icons .dropdown-toggle:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .dropdown-menu-dark {
            background-color: #343a40;
        }

        .alert {
            margin: 1rem auto;
            width: 90%;
            max-width: 500px;
        }

        /* Modal Backdrop Customization */
        .modal-backdrop {
            opacity: 0.8;
        }

        /* Modal Content Styling */
        .modal-content {
            background-color: rgba(0, 0, 0, 0.9);
            color: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
        }

        .modal-header {
            border-bottom: 1px solid #444;
        }

        .modal-body {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .modal-footer button {
            background-color: #007bff;
            color: white;
        }

        .modal-footer button:hover {
            background-color: #0056b3;
        }

        /* Styling the close button */
        .btn-close {
            background-color: #ff4747;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
        }

        .btn-close:hover {
            background-color: #ff0000;
        }
    </style>
</head>

<body>
    {{-- Top Left Logo --}}
    <div class="top-left-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Biometric Medical Access Logo">
    </div>

    {{-- Top Right Icons --}}
    <div class="top-right-icons">
        <a href="/" title="Home"><i class="fas fa-home"></i></a>
        {{-- Login Dropdown --}}
        <div class="dropdown">
            <a class="dropdown-toggle" href="#" role="button" id="loginDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Login">
                <i class="fas fa-sign-in-alt"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="loginDropdown">
                <li><a class="dropdown-item" href="{{ route('login') }}">Login as User</a></li>
                <li><a class="dropdown-item" href="{{ route('responder.login') }}">Login as Responder</a></li>
            </ul>
        </div>
        <a href="#" title="Signup" data-bs-toggle="modal" data-bs-target="#registerModal"><i class="fas fa-user-plus"></i></a>
        <a href="#" title="Tips" data-bs-toggle="modal" data-bs-target="#tipsModal"><i class="fas fa-lightbulb"></i></a>
    </div>

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info text-center">
            {{ session('info') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center">
            {{ session('error') }}
        </div>
    @endif

    {{-- Register Modal --}}
    <div class="modal" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Registration Consent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        By proceeding with registration, you agree to our terms and conditions as outlined in RA 10173 (Data Privacy Act). Below is a brief explanation of RA 10173:
                    </p>
                    <ul>
                        <li><strong>Transparency:</strong> Your personal data will be collected, stored, and processed securely.</li>
                        <li><strong>Purpose:</strong> Data collection is only for medical access purposes and will not be shared without consent.</li>
                        <li><strong>Rights:</strong> You have the right to access, correct, and request deletion of your data.</li>
                    </ul>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="consentCheckbox">
                        <label class="form-check-label" for="consentCheckbox">
                            I agree to the terms and conditions outlined in the Data Privacy Act.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="proceedButton" disabled>Proceed</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tips Modal --}}
    <div class="modal" id="tipsModal" tabindex="-1" aria-labelledby="tipsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tipsModalLabel">Helpful Tips</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul>
                        <li><strong>Tip 1:</strong> Keep your medical records updated for better access.</li>
                        <li><strong>Tip 2:</strong> Use biometric authentication for quick login.</li>
                        <li><strong>Tip 3:</strong> Make sure your device is secure to protect sensitive health data.</li>
                    </ul>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable the Proceed button only when the checkbox is checked
        const consentCheckbox = document.getElementById('consentCheckbox');
        const proceedButton = document.getElementById('proceedButton');

        consentCheckbox.addEventListener('change', function () {
            proceedButton.disabled = !this.checked;
        });

        // Redirect to step1.blade.php when "Proceed" is clicked
        proceedButton.addEventListener('click', function () {
            window.location.href = "{{ route('register.step1') }}";
        });
    </script>
</body>

</html>