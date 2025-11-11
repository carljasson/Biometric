<nav x-data="{ open: false, sidebarOpen: false }" class="bg-white border-b border-gray-100">
    <!-- Sidebar + Main Wrapper -->
    <div class="flex h-screen">

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
             class="fixed z-30 inset-y-0 left-0 w-64 bg-gray-800 overflow-y-auto transform transition-transform duration-300 ease-in-out">
            <div class="px-6 py-4 text-center text-white bg-gray-900">
                <i class="bi bi-person-circle text-6xl mb-2"></i>
                <h5 class="font-semibold">{{ Auth::user()->name ?? Auth::guard('admin')->user()->name ?? 'Guest' }}</h5>
            </div>

            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-2 text-white hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-house-door-fill me-2"></i> Home
                </a>

                <a href="{{ route('admin.users') }}" 
                   class="flex items-center px-4 py-2 text-white hover:bg-gray-700 {{ request()->routeIs('admin.users') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-people-fill me-2"></i> Manage Users
                </a>

                <a href="{{ route('admin.alerts') }}" 
                   class="flex items-center px-4 py-2 text-white hover:bg-gray-700">
                    <i class="bi bi-exclamation-triangle-fill text-yellow-400 me-2"></i> Emergency Alerts
                </a>

                <a href="{{ route('admin.admin-users') }}" 
                   class="flex items-center px-4 py-2 text-white hover:bg-gray-700 {{ request()->routeIs('admin.admin-users') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-shield-lock-fill me-2"></i> Admin User Management
                </a>

                <a href="{{ route('admin.add-responder') }}" 
                   class="flex items-center px-4 py-2 text-white hover:bg-gray-700 {{ request()->routeIs('admin.add-responder') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-plus-circle-fill me-2"></i> Add Responder
                </a>

                <a href="{{ route('admin.login-history') }}" 
                   class="flex items-center px-4 py-2 text-white hover:bg-gray-700 {{ request()->routeIs('admin.login-history') ? 'bg-gray-700 font-bold' : '' }}">
                    <i class="bi bi-clock-history me-2"></i> Login History
                </a>

                <a href="#" data-bs-toggle="modal" data-bs-target="#broadcastModal"
                   class="flex items-center px-4 py-2 text-white hover:bg-gray-700">
                    <i class="bi bi-megaphone-fill me-2"></i> Broadcast Messages
                </a>

                <form action="{{ route('logout') }}" method="POST" class="px-4 py-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-2 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col" :class="sidebarOpen ? 'ml-64' : 'ml-0'">

            <!-- Top Navigation -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center border-b border-gray-200 bg-white">

                    <!-- Left: Hamburger + Logo -->
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none sm:hidden mr-2">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ 'hidden': sidebarOpen, 'inline-flex': !sidebarOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ 'hidden': !sidebarOpen, 'inline-flex': sidebarOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        </a>
                    </div>

                    <!-- Right: Notification & Profile -->
                    <div class="flex items-center space-x-4">
                        <!-- Notification Bell -->
                        <div class="relative">
                            <button id="notificationBell" class="relative text-gray-600 hover:text-gray-800">
                                <i class="bi bi-bell-fill text-2xl"></i>
                                <span id="alertCount" class="absolute top-0 start-100 translate-middle badge rounded-full bg-red-500 hidden"></span>
                            </button>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="hidden sm:flex sm:items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none">
                                        <div>{{ Auth::user()->name ?? Auth::guard('admin')->user()->name ?? 'Guest' }}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">
                            {{ Auth::user()->name ?? Auth::guard('admin')->user()->name ?? 'Guest' }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">
                            {{ Auth::user()->email ?? Auth::guard('admin')->user()->email ?? '' }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</nav>
