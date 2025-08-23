<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color: #f8f9fa;">
@include('admin.broadcast-modal')

<!-- Sidebar -->
<div id="sidebar" class="sidebar" style="height: 100vh; width: 250px; background-color: #343a40; position: fixed; top: 0; left: -250px; transition: left 0.3s; z-index: 1050; overflow-y: auto;">
    <div class="sidebar-header" style="color: #fff; padding: 15px 20px; background-color: #212529; text-align: center;">
        <i class="bi bi-person-circle" style="font-size: 50px; color: #fff; margin-top: 10px;"></i>
        <h5 style="color: white;">{{ $admin->name }}</h5>
    </div>

    <a href="#" onclick="showDashboard()" class="text-white px-4 py-2 d-block text-decoration-none">
        <i class="bi bi-house-door-fill me-2"></i> Home
    </a>
    <a href="{{ route('admin.users') }}" class="text-white px-4 py-2 d-block text-decoration-none {{ request()->routeIs('admin.users') ? 'bg-secondary fw-bold' : '' }}">
        <i class="bi bi-people-fill me-2"></i> Manage Users
    </a>
    <a href="{{ route('admin.patients') }}"
       class="text-white px-4 py-2 d-block text-decoration-none {{ request()->routeIs('admin.patients') ? 'bg-secondary fw-bold' : '' }}">
        <i class="bi bi-folder2-open me-2"></i> Patient Management
    </a>
    <a href="{{ route('admin.records') }}" class="text-white px-4 py-2 d-block text-decoration-none">
        <i class="bi bi-folder2-open me-2"></i> Medical Records Access
    </a>
    <a href="{{ route('admin.alerts') }}" class="text-white px-4 py-2 d-block text-decoration-none">
        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Emergency Alerts
    </a>
    <a href="{{ route('admin.admin-users') }}"
       class="text-white px-4 py-2 d-block text-decoration-none {{ request()->routeIs('admin.admin-users') ? 'bg-secondary fw-bold' : '' }}">
        <i class="bi bi-shield-lock-fill me-2"></i> Admin User Management
    </a>
    <a href="{{ route('export.page') }}" class="text-white px-4 py-2 d-block text-decoration-none">
        <i class="bi bi-bar-chart-line-fill me-2"></i> Data Export
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
<div id="main" class="main-content" style="margin-left: 0; transition: margin-left 0.3s;">
    <div class="header bg-white shadow-sm p-3 d-flex justify-content-between align-items-center">
        <button class="menu-toggle" onclick="toggleSidebar()" style="background: none; border: none; font-size: 24px;">☰</button>
        <h4 class="m-0">Biometric Emergency Access</h4>

        <!-- 🔔 Notification Bell -->
        <div class="dropdown me-3">
            <button class="btn btn-light position-relative" id="notificationBell" data-bs-toggle="dropdown">
                <i class="bi bi-bell-fill fs-4"></i>
                <span id="alertCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                    
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end p-2" style="width: 300px; max-height: 400px; overflow-y: auto;" id="alertsList">
                <li class="text-center text-muted">No new alerts</li>
            </ul>
        </div>
    </div>

    <div class="container mt-4">

    <!-- Dashboard Overview -->
    <div id="dashboardOverview">

        <!-- 👤 User Stats -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-person-lines-fill me-2"></i>Total Users</h5>
                        <p class="card-text fs-4">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-calendar-week-fill me-2"></i>This Week</h5>
                        <p class="card-text fs-4">{{ $weeklyUsers }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-dark bg-warning shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-calendar-month-fill me-2"></i>This Month</h5>
                        <p class="card-text fs-4">{{ $monthlyUsers }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-calendar3 me-2"></i>This Year</h5>
                        <p class="card-text fs-4">{{ $yearlyUsers }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🏥 Patient Stats -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-dark shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-person-vcard-fill me-2"></i>Total Patients</h5>
                        <p class="card-text fs-4">{{ $totalPatients }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-calendar-week me-2"></i>This Week</h5>
                        <p class="card-text fs-4">{{ $weeklyPatients }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-dark bg-warning shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-calendar-month me-2"></i>This Month</h5>
                        <p class="card-text fs-4">{{ $monthlyPatients }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info shadow">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-calendar3 me-2"></i>This Year</h5>
                        <p class="card-text fs-4">{{ $yearlyPatients }}</p>
                    </div>
                </div>
            </div>
        </div>

  </div>
  
        <!-- ✅ Announcements Section -->
        <div id="announcementsSection">
            @if($announcements->count())
                <div class="mt-3">
                    @foreach($announcements as $announcement)
                        <div class="alert alert-info mb-2">
                            <strong>📢 {{ $announcement->created_at->format('F j, Y h:i A') }}</strong><br>
                            {{ $announcement->message }}
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary mt-3">
                    No announcements yet.
                </div>
            @endif
        </div>
<!-- 🔔 Notification Bell -->
<div class="dropdown me-3">
    <button class="btn btn-light position-relative" id="notificationBell" data-bs-toggle="dropdown">
        <i class="bi bi-bell-fill fs-4"></i>
        <span id="alertCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-2" 
        style="width: 300px; max-height: 400px; overflow-y: auto;" 
        id="alertsList">
        <li class="text-center text-muted">No new alerts</li>
    </ul>
</div>

<!-- 🚨 Emergency Light CSS -->
<style>
    .emergency-light {
        animation: flashRed 1s infinite alternate;
    }
    @keyframes flashRed {
        from { color: #dc3545; }
        to { color: #ff0000; }
    }
</style>
<style>
    /* Highlight new alerts */
    .new-alert {
        background-color: #f8d7da; /* light red */
        color: #721c24;
        border-radius: 5px;
        padding: 8px;
        margin-bottom: 5px;
        display: block;
        transition: background 0.3s;
    }

    /* When clicked, change to white */
    .new-alert.read {
        background-color: #ffffff !important;
        color: #000000 !important;
        font-weight: normal !important;
    }
</style>

<!-- 🔊 Emergency Alert Sound -->
<audio id="alertSound" src="https://actions.google.com/sounds/v1/alarms/alarm_clock.ogg" preload="auto"></audio>

<!-- Enable Bootstrap tooltips -->
<script>
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>

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

function showDashboard() {
    document.getElementById('dashboardOverview').style.display = 'block';
    document.getElementById('userTable').style.display = 'none';
    document.getElementById('announcementsSection').style.display = 'block';
}

function loadUsers() {
    document.getElementById('dashboardOverview').style.display = 'none';
    document.getElementById('userTable').style.display = 'block';
    document.getElementById('announcementsSection').style.display = 'none';
}

/* 🔔 Fetch Alerts */
/* 🔔 Fetch Alerts */
let lastAlertCount = 0; // track previous unread alerts

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

                    // Flashing bell
                    bellIcon.classList.add("emergency-light");

                    if (alertSound.paused) {
                        alertSound.loop = true;
                        alertSound.play().catch(e => console.log("Autoplay blocked:", e));
                    }

                    // SweetAlert popup for new alerts
                    if (unreadCount > lastAlertCount) {
                        Swal.fire({
                            title: '🚨 Emergency Alert!',
                            html: `<p>You have <strong>${unreadCount}</strong> new alert${unreadCount > 1 ? 's' : ''}.</p>`,
                            background: '#ff4d4d', // flashing red background
                            color: '#fff',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745', // ✅ custom green button
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
                                // Stop flashing when popup closes
                                Swal.getPopup().addEventListener('mouseleave', () => clearInterval(flashInterval));
                                
                                // Stop sound and flashing when OK clicked
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

                // Populate alerts
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

// Mark alerts as read + redirect
document.addEventListener("click", function(e) {
    const alertItem = e.target.closest(".mark-read-redirect");
    if (alertItem) {
        e.preventDefault();

        let alertId = alertItem.dataset.id; // get clicked alert ID

        // Turn only this alert white immediately
        alertItem.classList.add("read");

        fetch("{{ route('admin.mark-alerts-read') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: alertId }) // send only this ID
        })
        .then(res => res.json())
        .then(() => {
            window.location.href = "{{ route('admin.alerts') }}"; 
        });
    }
});

// Refresh every 10s
setInterval(fetchAlerts, 10000);
fetchAlerts();
</script>

@if(session('success'))
<script>
Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", confirmButtonColor: '#3085d6' });
</script>
@endif

@if($errors->any())
<script>
Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ $errors->first() }}", confirmButtonColor: '#d33' });
</script>
@endif

@if(session('showPatients'))
<script>
    loadUsers();
</script>
@endif

</body>
</html>
