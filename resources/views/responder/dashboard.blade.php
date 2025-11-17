@extends('layouts.responder')

@section('content')
<div class="container mt-4 mb-5">

    <div class="card shadow">

        {{-- 🔔 HEADER WITH NOTIFICATION BELL --}}
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4>Welcome, {{ auth('responder')->user()->name }}!</h4>

            <!-- 🔔 Notification Bell -->
            <div class="position-relative" id="notificationBellWrapper" style="cursor:pointer;">
                <i class="fas fa-bell" id="notificationBell" style="font-size:22px; color:white;"></i>
                <span id="notificationCount"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    style="display:none;">
                </span>
            </div>

            <!-- 🔽 Dropdown List -->
            <div id="notificationDropdown"
                class="card shadow"
                style="display:none; position:absolute; right:20px; top:60px; width:260px; z-index:1500;">
                <ul class="list-group" id="notificationList">
                    <!-- Alerts load here -->
                </ul>
            </div>
        </div>

        <div class="card-body">

            {{-- 📢 Announcements --}}
            @if(isset($announcements) && $announcements->count())
                <div class="mt-2">
                    @foreach($announcements as $announcement)
                        <div class="alert alert-info bg-white text-dark mb-2">
                            <strong>📢 {{ $announcement->created_at->format('F j, Y h:i A') }}</strong><br>
                            {{ $announcement->message }}
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-secondary bg-white text-dark mt-3">
                    No announcements yet.
                </div>
            @endif

            {{-- 🚨 Emergency Instructions --}}
            <div class="alert alert-danger mt-4">
                <h5>🚨 Emergency Step-by-Step Guide</h5>
                <ol class="mb-0">
                    <li>Stay calm and assess the situation.</li>
                    <li>Call emergency services or notify nearby people.</li>
                    <li>Use this app to scan the patient's fingerprint or face to access medical info.</li>
                    <li>Follow the emergency instructions provided in their profile.</li>
                    <li>Keep the patient stable while waiting for help.</li>
                </ol>
            </div>

        </div>
    </div>
</div>

{{-- ✅ Bottom Navigation --}}
<div class="fixed-bottom-nav">
    <a href="{{ route('responder.dashboard') }}"
       class="{{ request()->routeIs('responder.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i><span>Home</span>
    </a>

    <a href="{{ route('responder.profile') }}"
       class="{{ request()->routeIs('responder.profile') ? 'active' : '' }}">
        <i class="fas fa-user-circle"></i><span>Profile</span>
    </a>

    <a href="{{ route('responder.scan.fingerprint') }}"
       class="{{ request()->routeIs('responder.scan.fingerprint') ? 'active' : '' }}">
        <i class="fas fa-fingerprint"></i><span>Fingerprint</span>
    </a>

    <a href="{{ route('responder.scan.face') }}"
       class="{{ request()->routeIs('responder.scan.face') ? 'active' : '' }}">
        <i class="fas fa-camera"></i><span>Face Scan</span>
    </a>

    {{-- 🚨 Alerts Button --}}
    <a id="alertIcon" href="{{ route('responder.alerts.view') }}"
       class="alert-notify {{ request()->routeIs('responder.alerts.view') ? 'active' : '' }}">
        <i class="fas fa-triangle-exclamation text-danger"></i>
        <span>Alerts</span>
    </a>

    <a href="{{ route('responder.logout') }}">
        <i class="fas fa-sign-out-alt text-danger"></i><span>Logout</span>
    </a>
</div>

{{-- 🌐 Bottom Navigation Styling --}}
<style>
.fixed-bottom-nav {
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #fff;
    border-top: 1px solid #ccc;
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 8px 0;
    z-index: 1000;
}
.fixed-bottom-nav a {
    color: #333;
    text-align: center;
    text-decoration: none;
    font-size: 12px;
}
.fixed-bottom-nav a i {
    font-size: 20px;
    display: block;
}
.fixed-bottom-nav a.active {
    color: #0d6efd;
    font-weight: bold;
}
.blink-alert { animation: blink 1s infinite; }
@keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0; }
    100% { opacity: 1; }
}
</style>

{{-- 🌐 Emergency + Notification Bell Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ---------------------------
       ALERT ICON (BOTTOM NAV) LOGIC
    -----------------------------*/
    setInterval(checkEmergencyAlerts, 10000);

    function toggleAlertIcon(hasAlerts) {
        const icon = document.querySelector('#alertIcon i');
        if (hasAlerts) icon.classList.add('blink-alert');
        else icon.classList.remove('blink-alert');
    }

    function checkEmergencyAlerts() {
        fetch("{{ route('responder.alerts.check') }}")
            .then(response => response.json())
            .then(data => {
                toggleAlertIcon(data.length > 0);
            });
    }

    /* ---------------------------
       🔔 NOTIFICATION BELL LOGIC
    -----------------------------*/

    const bell = document.getElementById('notificationBell');
    const countBadge = document.getElementById('notificationCount');
    const dropdown = document.getElementById('notificationDropdown');
    const list = document.getElementById('notificationList');
    const wrapper = document.getElementById('notificationBellWrapper');

    wrapper.addEventListener('click', () => {
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    });

    setInterval(fetchBellNotifications, 10000);

    fetchBellNotifications();

    function fetchBellNotifications() {
        fetch("{{ route('responder.alerts.check') }}")
            .then(response => response.json())
            .then(alerts => {
                updateBell(alerts);
                updateDropdown(alerts);
            });
    }

    function updateBell(alerts) {
        let count = alerts.length;

        if (count > 0) {
            bell.style.color = "yellow";
            countBadge.innerText = count;
            countBadge.style.display = "inline-block";
        } else {
            bell.style.color = "white";
            countBadge.style.display = "none";
        }
    }

    function updateDropdown(alerts) {
        list.innerHTML = "";

        if (alerts.length === 0) {
            list.innerHTML = `
                <li class="list-group-item text-center text-muted">
                    No alerts
                </li>
            `;
            return;
        }

        alerts.forEach(alert => {
            let item = document.createElement("li");
            item.className = "list-group-item";
            item.style.cursor = "pointer";

            item.innerHTML = `
                <strong>🚨 ${alert.type}</strong><br>
                <small>${alert.created_at}</small>
            `;

            item.addEventListener("click", () => {
                Swal.fire({
                    title: "🚨 Emergency Alert",
                    html: `
                        <strong>Type:</strong> ${alert.type}<br>
                        <strong>Sender:</strong> ${alert.sender_name}<br>
                        <strong>Email:</strong> ${alert.sender_email}<br>
                        <strong>Phone:</strong> ${alert.sender_phone}<br>
                        <strong>Destination:</strong> ${alert.destination}<br>
                        <strong>Location:</strong>
                        <a href="https://www.google.com/maps?q=${alert.latitude},${alert.longitude}" target="_blank">
                            📍 View Map
                        </a><br><br>
                        ${alert.photo ? `<img src="${alert.photo}" style="max-width:100%; border-radius:8px;">` : ''}
                    `,
                    icon: "warning"
                });
            });

            list.appendChild(item);
        });
    }

});
</script>
@endsection
