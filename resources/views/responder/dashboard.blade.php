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

{{-- Emergency Sound --}}
<audio id="alertSound" src="{{ asset('sounds/emergency.mp3') }}" preload="auto"></audio>

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

    // Track alerts acknowledged this session
    let acknowledgedAlertIds = JSON.parse(sessionStorage.getItem('acknowledgedAlertIds') || '[]');
    let lastAlertId = sessionStorage.getItem('lastAlertId') || 0; // track highest alert id already seen
    const bell = document.getElementById('notificationBell');
    const countBadge = document.getElementById('notificationCount');
    const dropdown = document.getElementById('notificationDropdown');
    const list = document.getElementById('notificationList');
    const wrapper = document.getElementById('notificationBellWrapper');
    const alertSound = document.getElementById('alertSound');

    wrapper.addEventListener('click', () => {
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    
    // Mark current visible alerts as acknowledged
    const currentAlerts = Array.from(list.children).map(li => parseInt(li.dataset.id));
    currentAlerts.forEach(id => {
        if (!acknowledgedAlertIds.includes(id)) {
            acknowledgedAlertIds.push(id);
        }
    });
    sessionStorage.setItem('acknowledgedAlertIds', JSON.stringify(acknowledgedAlertIds));

    // Update bell count immediately
    fetch("{{ route('responder.alerts.check') }}")
        .then(res => res.json())
        .then(alerts => updateBell(alerts));

    markAlertsAsRead();
});


    setInterval(checkEmergencyAlerts, 10000);
    checkEmergencyAlerts();

    function checkEmergencyAlerts() {
        fetch("{{ route('responder.alerts.check') }}")
            .then(response => response.json())
            .then(data => {
                const icon = document.querySelector('#alertIcon i');
                if (data.length > 0) icon.classList.add('blink-alert');
                else icon.classList.remove('blink-alert');
            });
    }

    setInterval(fetchAlerts, 10000);
    fetchAlerts();

    function fetchAlerts() {
        fetch("{{ route('responder.alerts.check') }}")
            .then(res => res.json())
            .then(alerts => {
                updateBell(alerts);
                updateDropdown(alerts);
                showNewAlerts(alerts);
            });
    }

    function updateBell(alerts) {
        let unreadCount = alerts.filter(a => !acknowledgedAlertIds.includes(a.id)).length;
        if (unreadCount > 0) {
            bell.style.color = "yellow";
            countBadge.innerText = unreadCount;
            countBadge.style.display = "inline-block";
        } else {
            bell.style.color = "white";
            countBadge.style.display = "none";
        }
    }

    function updateDropdown(alerts) {
        list.innerHTML = "";
        if (alerts.length === 0) {
            list.innerHTML = `<li class="list-group-item text-center text-muted">No alerts</li>`;
            return;
        }
       alerts.forEach(alert => {
    let item = document.createElement("li");
    item.className = "list-group-item";
    item.style.cursor = "pointer";
    item.dataset.id = alert.id; // <-- important for tracking
    item.innerHTML = `<strong>🚨 ${alert.type}</strong><br><small>${alert.created_at}</small>`;
    item.addEventListener("click", () => {
        window.location.href = "{{ route('responder.alerts.view') }}";
    });
    list.appendChild(item);
});

    }

    function showNewAlerts(alerts) {
        // Only show alerts with ID greater than lastAlertId
        alerts.forEach(alert => {
            if (alert.id > lastAlertId && !acknowledgedAlertIds.includes(alert.id) && alert.status !== 'resolved' && alert.status !== 'read') {

                alertSound.currentTime = 0;
                alertSound.play().catch(e => console.log('Audio play blocked:', e));

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
                        ${alert.photo ? `<img src="${alert.photo}" class="img-fluid rounded" style="max-height: 250px; border: 1px solid #ccc;">` : ''}
                    `,
                    icon: "warning",
                    showCancelButton: false,
                    confirmButtonText: "OK",
                }).then(() => {
                    acknowledgedAlertIds.push(alert.id);
                    sessionStorage.setItem('acknowledgedAlertIds', JSON.stringify(acknowledgedAlertIds));
                    if (alert.id > lastAlertId) lastAlertId = alert.id;
                    sessionStorage.setItem('lastAlertId', lastAlertId);
                });
            }
        });
    }

    function markAlertsAsRead() {
        if (acknowledgedAlertIds.length === 0) return;
        fetch("{{ route('responder.alerts.markRead') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ alert_ids: acknowledgedAlertIds })
        }).then(() => {
            countBadge.style.display = "none";
            bell.style.color = "white";
        });
    }

    @if(session('logout_success'))
        Swal.fire({
            icon: 'success',
            title: 'Logged Out',
            text: '{{ session('logout_success') }}',
            timer: 2500,
            showConfirmButton: false,
            timerProgressBar: true
        });
    @endif

});


</script>
@endsection
