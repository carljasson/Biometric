<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Tailwind (optional for utility classes) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased">

<div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
        class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white transform transition-transform duration-300 shadow-lg z-20">

        <!-- Sidebar Header -->
        <div class="px-6 py-4 text-center bg-gray-900">
            <i class="bi bi-person-circle text-6xl mb-2"></i>
            <h5 class="font-semibold">
                {{ Auth::user()->name ?? Auth::guard('admin')->user()->name ?? 'Guest' }}
            </h5>
        </div>

        <!-- Sidebar Links -->
        <nav class="mt-4 flex flex-col">
            <x-sidebar-link route="admin.dashboard" icon="house-door-fill" label="Home" />
            <x-sidebar-link route="admin.users" icon="people-fill" label="Manage Users" />
            <x-sidebar-link route="admin.alerts" icon="exclamation-triangle-fill" label="Emergency Alerts" color="text-yellow-400" />
            <x-sidebar-link route="admin.admin-users" icon="shield-lock-fill" label="Admin User Management" />
            <x-sidebar-link route="admin.add-responder" icon="plus-circle-fill" label="Add Responder" />
            <x-sidebar-link route="admin.login-history" icon="clock-history" label="Login History" />
            <a href="#" data-bs-toggle="modal" data-bs-target="#broadcastModal"
               class="flex items-center px-4 py-2 hover:bg-gray-700">
                <i class="bi bi-megaphone-fill me-2"></i> Broadcast Messages
            </a>

            <form action="{{ route('admin.logout') }}" method="POST" class="px-4 py-2 mt-auto">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-2 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0 sm:ml-64'">

        <!-- Top Navigation -->
        <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-white border-b border-gray-200 flex justify-between items-center h-16">

            <!-- Hamburger (mobile) -->
            <div class="flex items-center sm:hidden">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': sidebarOpen, 'inline-flex': !sidebarOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-auto p-4">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
