<!-- resources/views/admin/login-pin.blade.php -->
<!doctype html>
<html>
<head>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Enter PIN</title>
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
  <div class="card p-4" style="width:360px;">
    <h5 class="mb-3 text-center">Enter verification PIN</h5>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.pin.verify') }}">
      @csrf
      <div class="mb-3">
        <input type="text" name="pin" maxlength="6" class="form-control text-center fs-4" required inputmode="numeric" autofocus>
        <small class="text-muted d-block text-center mt-2">A PIN was sent to your email. Expires in 5 minutes.</small>
      </div>
      <button class="btn btn-primary w-100">Verify PIN</button>
    </form>

    <div class="text-center mt-3">
      <a href="{{ route('admin.resend.pin') }}">Resend PIN</a>
    </div>
  </div>
</body>
</html>
