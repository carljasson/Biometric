<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Responder Page')</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            color: #000;
            padding-bottom: 70px; /* for bottom nav */
        }

        .fixed-bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #fff;
            border-top: 1px solid #ccc;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
        }

        .fixed-bottom-nav a {
            color: #333;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
        }

        .fixed-bottom-nav a.active {
            color: #0d6efd;
            font-weight: bold;
        }

        .fixed-bottom-nav i {
            font-size: 20px;
            display: block;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex flex-column">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
