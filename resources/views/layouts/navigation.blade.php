<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-gray-100">

<nav x-data="{ sidebarOpen: false }" class="flex h-screen">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white transform transition-transform duration-300 shadow-lg z-20">
        <div class="px-6 py-4 text-center bg-gray-900">
            <i class="bi bi-person-circle text-6xl mb-2"></i>
            <h5 class="font-semibold">{{ Auth::user()->name ?? Auth::guard('admin')->user()->name ?? 'Admin' }}</h5>
        </div>

        <nav class="mt-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-house-door-fill me-2"></i> Home
            </a>

            <a href="{{ route('admin.users') }}"
               class="flex items-center px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.users') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-people-fill me-2"></i> Manage Users
            </a>

            <a href="{{ route('admin.alerts') }}"
               class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                <i class="bi bi-exclamation-triangle-fill text-yellow-400 me-2"></i> Emergency Alerts
            </a>

            <a href="{{ route('admin.admin-users') }}"
               class="flex items-center px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.admin-users') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-shield-lock-fill me-2"></i> Admin Users
            </a>

            <a href="{{ route('admin.add-responder') }}"
               class="flex items-center px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.add-responder') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-plus-circle-fill me-2"></i> Add Responder
            </a>

            <a href="{{ route('admin.login-history') }}"
               class="flex items-center px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.login-history') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-clock-history me-2"></i> Login History
            </a>

            <a href="#" data-bs-toggle="modal" data-bs-target="#broadcastModal"
               class="flex items-center px-4 py-2 hover:bg-gray-700 rounded">
                <i class="bi bi-megaphone-fill me-2"></i> Broadcast
            </a>

            <form action="{{ route('admin.logout') }}" method="POST" class="px-4 py-2">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-2 py-2 bg-gray-700 hover:bg-gray-600 rounded">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col" :class="sidebarOpen ? 'ml-64' : 'ml-0' transition-all duration-300">

        <!-- Top Bar -->
        <header class="flex items-center justify-between bg-white shadow px-4 h-16">
            <!-- Hamburger -->
            <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden text-gray-500">
                <i class="bi bi-list text-2xl"></i>
            </button>

            <!-- Title -->
            <h1 class="text-xl font-semibold">Admin Dashboard</h1>

            <!-- Notification -->
            <div class="relative">
                <button id="notificationBell" class="relative text-gray-600 hover:text-gray-800">
                    <i class="bi bi-bell-fill text-2xl"></i>
                    <span id="alertCount" class="absolute top-0 start-100 translate-middle badge rounded-full bg-red-500 hidden"></span>
                </button>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</nav>

<!-- Include your broadcast modal here -->
@include('admin.broadcast-modal')

</body>
</html>
