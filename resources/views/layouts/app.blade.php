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

    <!-- Tailwind (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body, html { margin: 0; padding: 0; height: 100%; }
        main { padding-top: 0 !important; }

        /* Emergency Bell Flash */
        .emergency-light {
            animation: flashRed 1s infinite alternate;
        }
        @keyframes flashRed {
            from { color: #dc3545; }
            to { color: #ff0000; }
        }

        /* Alert Dropdown Highlight */
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
<body class="font-sans antialiased">

<div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full sm:translate-x-0'"
        class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white transform transition-transform duration-300 shadow-lg z-20">

        <!-- Sidebar Header -->
        <div class="px-6 py-4 text-center bg-gray-900">
            <i class="bi bi-person-circle text-6xl mb-2"></i>
            <h5 class="font-semibold">{{ Auth::user()->name ?? Auth::guard('admin')->user()->name ?? 'Guest' }}</h5>
        </div>

        <!-- Sidebar Links -->
        <nav class="mt-4 flex flex-col">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-house-door-fill me-2"></i> Home
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.users') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-people-fill me-2"></i> Manage Users
            </a>
            <a href="{{ route('admin.alerts') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                <i class="bi bi-exclamation-triangle-fill text-yellow-400 me-2"></i> Emergency Alerts
            </a>
            <a href="{{ route('admin.admin-users') }}" class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.admin-users') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-shield-lock-fill me-2"></i> Admin User Management
            </a>
            <a href="{{ route('admin.add-responder') }}" class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.add-responder') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-plus-circle-fill me-2"></i> Add Responder
            </a>
            <a href="{{ route('admin.login-history') }}" class="flex items-center px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.login-history') ? 'bg-gray-700 font-bold' : '' }}">
                <i class="bi bi-clock-history me-2"></i> Login History
            </a>

            <!-- Broadcast Modal Trigger -->
            <a href="#" data-bs-toggle="modal" data-bs-target="#broadcastModal" class="flex items-center px-4 py-2 hover:bg-gray-700">
                <i class="bi bi-megaphone-fill me-2"></i> Broadcast Messages
            </a>
     <!-- ------------------------
             Backup Manager Section
             ------------------------ -->
        <div id="backupManager" style="display: none;" class="mt-4">
            <div class="card mb-3 p-3">
                <h5 class="mb-3"><i class="bi bi-hdd-fill me-2"></i>Backup Manager</h5>

                <form action="{{ route('admin.backups.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2 align-items-center">
                        <div class="col-auto" style="min-width: 300px;">
                            <input type="file" name="backup_file" class="form-control" required>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary">Upload Backup</button>
                        </div>
                        <div class="col-auto">
                            <small class="text-muted">Max: 100MB (change validation in controller)</small>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card p-3">
                <h6>Uploaded Backups</h6>
                @if(isset($backups) && $backups->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Size</th>
                                    <th>Modified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $b)
                                    <tr>
                                        <td>{{ $b['name'] }}</td>
                                        <td>{{ number_format($b['size'] / 1024, 2) }} KB</td>
                                        <td>{{ $b['modified'] }}</td>
                                        <td>
                                            <a href="{{ route('admin.backups.download', $b['name']) }}" class="btn btn-sm btn-success">
                                                <i class="bi bi-download"></i> Download
                                            </a>

                                            <form action="{{ route('admin.backups.destroy', $b['name']) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete {{ $b['name'] }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-secondary">No backups found.</div>
                @endif
            </div>
        </div>

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

        <!-- Header -->
        <div class="header fixed top-0 left-0 right-0 z-10 bg-white shadow-sm p-3 d-flex justify-content-between align-items-center"
             :style="sidebarOpen ? 'margin-left:16rem;' : 'margin-left:0;'">
            <h4 class="m-0">Biometric Emergency Access</h4>

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
        <main class="flex-1 overflow-auto p-4 mt-16">
            @yield('content')
        </main>
    </div>

    <!-- Broadcast Modal -->
    @include('admin.broadcast-modal')

    <!-- Blade Scripts --> @stack('scripts')

    <!-- Alert Sound -->
    <audio id="alertSound" src="https://actions.google.com/sounds/v1/alarms/alarm_clock.ogg" preload="auto"></audio>
</div>

<script>
let lastAlertCount = 0;

function fetchAlerts() {
    fetch("{{ route('admin.fetch-alerts') }}")
        .then(res => res.json())
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
                        alertSound.play().catch(e=>console.log("Autoplay blocked:", e));
                    }

                    if (unreadCount > lastAlertCount) {
                        Swal.fire({
                            title: '🚨 Emergency Alert!',
                            html: `<p>You have <strong>${unreadCount}</strong> new alert${unreadCount>1?'s':''}.</p>`,
                            background:'#ff4d4d', color:'#fff',
                            confirmButtonText:'OK', confirmButtonColor:'#28a745',
                            timer:10000, timerProgressBar:true,
                            didOpen:()=>{
                                const swalPopup=Swal.getHtmlContainer();
                                let flash=true;
                                const flashInterval=setInterval(()=>{ if(swalPopup){ swalPopup.style.backgroundColor=flash?'#ff0000':'#ff4d4d'; flash=!flash;} },500);
                                Swal.getConfirmButton().addEventListener('click',()=>{ clearInterval(flashInterval); alertSound.pause(); alertSound.currentTime=0; bellIcon.classList.remove("emergency-light"); });
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

                data.alerts.forEach(alert=>{
                    const li=document.createElement('li');
                    li.innerHTML=`<a href="#" class="dropdown-item mark-read-redirect ${alert.read?'read':'new-alert'}" data-id="${alert.id}">🚨 <strong>${alert.type}</strong><br><small>${new Date(alert.created_at).toLocaleString()}</small></a>`;
                    alertsList.appendChild(li);
                });
            } else {
                alertCountEl.classList.add('d-none');
                alertsList.innerHTML='<li class="text-center text-muted">No new alerts</li>';
                bellIcon.classList.remove("emergency-light");
                alertSound.pause(); alertSound.currentTime=0;
            }

            lastAlertCount=unreadCount;
        })
        .catch(console.error);
}

document.addEventListener("click", e=>{
    const alertItem=e.target.closest(".mark-read-redirect");
    if(alertItem){
        e.preventDefault();
        let alertId=alertItem.dataset.id;
        alertItem.classList.add("read");
        fetch("{{ route('admin.mark-alerts-read') }}", {
            method:"POST",
            headers:{
                "X-CSRF-TOKEN":"{{ csrf_token() }}",
                "Content-Type":"application/json"
            },
            body: JSON.stringify({id:alertId})
        }).then(res=>res.json()).then(()=>{ window.location.href="{{ route('admin.alerts') }}"; });
    }
});

setInterval(fetchAlerts,10000);
fetchAlerts();
</script>

@if(session('success'))
<script>Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", confirmButtonColor: '#3085d6' });</script>
@endif
@if($errors->any())
<script>Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ $errors->first() }}", confirmButtonColor: '#d33' });</script>
@endif

</body>
</html>
