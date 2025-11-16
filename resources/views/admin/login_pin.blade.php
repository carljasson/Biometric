<!DOCTYPE html>
<html>
<head>
    <title>Enter Login PIN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
<div class="card p-4 shadow" style="width: 380px;">
    <h4 class="text-center mb-3">Enter Verification PIN</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('admin.login.pin.verify') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Enter 6-digit PIN</label>
            <input type="text" name="pin" maxlength="6" inputmode="numeric" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Verify PIN</button>
    </form>

    <p class="text-center mt-3 small text-muted">
        A verification PIN was sent to your email.
    </p>
</div>
</body>
</html>
