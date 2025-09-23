<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Biometric Access')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @stack('styles')

    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #f8f9fa;
        }

        #sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background-color: #343a40;
            z-index: 1050;
            overflow-y: auto;
            transition: left 0.3s;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s;
            min-height: 100vh;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .menu-toggle {
            font-size: 20px;
            background: none;
            border: none;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* 🔔 Notification Bell */
        .emergency-light {
            animation: flashRed 1s infinite alternate;
        }
        @keyframes flashRed {
            from { color: #dc3545; }
            to { color: #ff0000; }
        }

        .new-alert {
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 5px;
            display: block;
            transition: background 0.3s;
        }

        .new-alert.read {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-weight: normal !important;
        }
    </style>
</head>
<body>

<!-- Broadcast Modal -->
@include('admin.broadcast-modal')

<!-- Sidebar -->
<div id="sidebar">
    <div class="sidebar-header text-center text-white bg-dark py-3">
        <i class="bi bi-person-circle fs-1"></i>
        <h5>{{ $admin?->name ?? 'Admin' }}</h5>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="text-white px-4 py-2 d-block text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'bg-secondary fw-bold' : '' }}">
        <i class="bi bi-house-door-fill me-2"></i> Home
    </a>
    <a href="{{ route('admin.users') }}" class="text-white px-4 py-2 d-block text-decoration-none {{ request()->routeIs('admin.users') ? 'bg-secondary fw-bold' : '' }}">
        <i class="bi bi-people-fill me-2"></i> Manage Users
    </a>
    <a href="{{ route('admin.alerts') }}" class="text-white px-4 py-2 d-block text-decoration-none">
        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Emergency Alerts
    </a>
    <a href="{{ route('admin.admin-users') }}" class="text-white px-4 py-2 d-block text-decoration-none {{ request()->routeIs('admin.admin-users') ? 'bg-secondary fw-bold' : '' }}">
        <i class="bi bi-shield-lock-fill me-2"></i> Admin User Management
    </a>
    
    <a href="#" data-bs-toggle="modal" data-bs-target="#broadcastModal" class="text-white px-4 py-2 d-block text-decoration-none">
        <i class="bi bi-megaphone-fill me-2"></i> Broadcast Messages
    </a>

    <form action="{{ route('admin.logout') }}" method="POST" class="m-3">
        @csrf
        <button type="submit" class="btn btn-outline-light w-100">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
    </form>
</div>

<!-- Main Content -->
<div id="main" class="main-content">
    <!-- Header -->
    <div class="header bg-white shadow-sm px-3 py-2 d-flex justify-content-between align-items-center">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <h5 class="mb-0">Biometric Emergency Access</h5>

        <!-- 🔔 Notification Bell -->
        <div class="dropdown me-3">
            <button class="btn btn-light position-relative" id="notificationBell" data-bs-toggle="dropdown">
                <i class="bi bi-bell-fill fs-4"></i>
                <span id="alertCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end p-2" style="width: 300px; max-height: 400px; overflow-y: auto;" id="alertsList">
                <li class="text-center text-muted">No new alerts</li>
            </ul>
        </div>
    </div>

    <!-- Page Content -->
    <div id="mainContent" class="p-3">
        @yield('content')
    </div>
</div>

<!-- JS Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<audio id="alertSound" src="https://actions.google.com/sounds/v1/alarms/alarm_clock.ogg" preload="auto"></audio>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const isOpen = sidebar.style.left === '0px';
    sidebar.style.left = isOpen ? '-250px' : '0px';
    main.style.marginLeft = isOpen ? '0' : '250px';
    localStorage.setItem('sidebarOpen', !isOpen);
}

document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const savedState = localStorage.getItem('sidebarOpen');
    if (savedState === 'false') {
        sidebar.style.left = '-250px';
        main.style.marginLeft = '0';
    } else {
        sidebar.style.left = '0';
        main.style.marginLeft = '250px';
    }
});

/* 🔔 Fetch Alerts */
let lastAlertCount = 0;

function fetchAlerts() {
    fetch("{{ route('admin.fetch-alerts') }}")
        .then(response => response.json())
        .then(data => {
            const alertCountEl = document.getElementById('alertCount');
            const alertsList = document.getElementById('alertsList');
            const bellIcon = document.querySelector('#notificationBell i'); 
            const alertSound = document.getElementById('alertSound');
            alertsList.innerHTML = '';

            const unreadCount = data.unreadCount;

            if (data.alerts.length > 0) {
                if(unreadCount > 0){
                    alertCountEl.textContent = unreadCount;
                    alertCountEl.classList.remove('d-none');
                    bellIcon.classList.add("emergency-light");

                    if (alertSound.paused) {
                        alertSound.loop = true;
                        alertSound.play().catch(e => console.log("Autoplay blocked:", e));
                    }

                    if (unreadCount > lastAlertCount) {
                        Swal.fire({
                            title: '🚨 Emergency Alert!',
                            html: `<p>You have <strong>${unreadCount}</strong> new alert${unreadCount > 1 ? 's' : ''}.</p>`,
                            background: '#ff4d4d',
                            color: '#fff',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745',
                            timer: 10000,
                            timerProgressBar: true,
                            didOpen: () => {
                                const swalPopup = Swal.getHtmlContainer();
                                let flash = true;
                                const flashInterval = setInterval(() => {
                                    if (swalPopup) {
                                        swalPopup.style.backgroundColor = flash ? '#ff0000' : '#ff4d4d';
                                        flash = !flash;
                                    }
                                }, 500);

                                Swal.getConfirmButton().addEventListener('click', () => {
                                    clearInterval(flashInterval);
                                    alertSound.pause();
                                    alertSound.currentTime = 0;
                                    bellIcon.classList.remove("emergency-light");
                                });
                            }
                        });
                    }
                } else {
                    alertCountEl.textContent = 0;
                    alertCountEl.classList.add('d-none');
                    bellIcon.classList.remove("emergency-light");
                    alertSound.pause();
                    alertSound.currentTime = 0;
                }

                data.alerts.forEach(alert => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <a href="#" class="dropdown-item mark-read-redirect ${alert.read ? 'read' : 'new-alert'}" data-id="${alert.id}">
                            🚨 <strong>${alert.type}</strong><br>
                            <small>${new Date(alert.created_at).toLocaleString()}</small>
                        </a>
                    `;
                    alertsList.appendChild(li);
                });
            } else {
                alertCountEl.textContent = 0;
                alertCountEl.classList.add('d-none');
                alertsList.innerHTML = '<li class="text-center text-muted">No new alerts</li>';
                bellIcon.classList.remove("emergency-light");
                alertSound.pause();
                alertSound.currentTime = 0;
            }

            lastAlertCount = unreadCount;
        })
        .catch(error => console.error('Error fetching alerts:', error));
}

/* Mark alerts as read + redirect */
document.addEventListener("click", function(e) {
    const alertItem = e.target.closest(".mark-read-redirect");
    if (alertItem) {
        e.preventDefault();
        let alertId = alertItem.dataset.id;
        alertItem.classList.add("read");

        fetch("{{ route('admin.mark-alerts-read') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: alertId })
        })
        .then(res => res.json())
        .then(() => {
            window.location.href = "{{ route('admin.alerts') }}"; 
        });
    }
});

setInterval(fetchAlerts, 10000);
fetchAlerts();
</script>

@if(session('success'))
<script>
Swal.fire({ icon: 'success', title: 'Success!', text: @json(session('success')), confirmButtonColor: '#3085d6' });
</script>
@endif

@if($errors->any())
<script>
Swal.fire({ icon: 'error', title: 'Error', text: @json($errors->first()), confirmButtonColor: '#d33' });
</script>
@endif

@stack('scripts')
</body>
</html>
