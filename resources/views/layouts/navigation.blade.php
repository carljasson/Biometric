<nav x-data="{ sidebarOpen: false }" class="bg-white border-b border-gray-100 h-screen">
    <div class="flex h-full">

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed z-30 inset-y-0 left-0 w-64 bg-gray-800 text-white overflow-y-auto transform transition-transform duration-300 ease-in-out">
            <div class="px-6 py-4 text-center bg-gray-900">
                <i class="bi bi-person-circle text-6xl mb-2"></i>
                <h5 class="font-semibold">{{ $admin->name ?? Auth::user()->name ?? 'Guest' }}</h5>
            </div>

            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-house-door-fill me-2"></i> Home
                </a>

                <a href="{{ route('admin.users') }}" 
                   class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.users') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-people-fill me-2"></i> Manage Users
                </a>

                <a href="{{ route('admin.alerts') }}" 
                   class="flex items-center px-4 py-2 hover:bg-gray-700">
                    <i class="bi bi-exclamation-triangle-fill text-yellow-400 me-2"></i> Emergency Alerts
                </a>

                <a href="{{ route('admin.admin-users') }}" 
                   class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.admin-users') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-shield-lock-fill me-2"></i> Admin User Management
                </a>

                <a href="{{ route('admin.add-responder') }}" 
                   class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.add-responder') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-plus-circle-fill me-2"></i> Add Responder
                </a>

                <a href="{{ route('admin.login-history') }}" 
                   class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.login-history') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-clock-history me-2"></i> Login History
                </a>

                <a href="#" data-bs-toggle="modal" data-bs-target="#broadcastModal"
                   class="flex items-center px-4 py-2 hover:bg-gray-700">
                    <i class="bi bi-megaphone-fill me-2"></i> Broadcast Messages
                </a>

                <form action="{{ route('admin.logout') }}" method="POST" class="px-4 py-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-2 py-2 bg-gray-700 hover:bg-gray-600 rounded">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col" :class="sidebarOpen ? 'ml-64' : 'ml-0'">

            <!-- Top Navigation -->
            <header class="flex justify-between items-center border-b border-gray-200 px-4 py-2 bg-white">
                <!-- Hamburger Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': sidebarOpen, 'inline-flex': !sidebarOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'hidden': !sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Notification Bell -->
                <div class="relative">
                    <button id="notificationBell" class="relative text-gray-600 hover:text-gray-800">
                        <i class="bi bi-bell-fill text-2xl"></i>
                        <span id="alertCount" class="absolute top-0 start-100 translate-middle badge rounded-full bg-red-500 hidden"></span>
                    </button>
                </div>
            </header>

            <!-- Main slot for page content -->
            <main class="flex-1 overflow-y-auto p-4">
                {{ $slot ?? '' }}
            </main>

        </div>
    </div>
</nav>
