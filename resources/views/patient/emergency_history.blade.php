@extends('layouts.patients')

@section('content')
<div class="container mt-4">
    <h2 class="text-center text-danger mb-4">My Sent Alerts</h2>

    <div id="alertsContainer" class="list-group">
        {{-- AJAX will load alerts here --}}
    </div>
</div>

<script>
    async function loadAlerts() {
        try {
            const res = await fetch('{{ route("patient.alertHistory") }}');
            const html = await res.text();
            document.getElementById('alertsContainer').innerHTML = html;
        } catch(err) {
            console.error(err);
        }
    }

    // Initial load
    loadAlerts();

    // Auto refresh every 10 seconds
    setInterval(loadAlerts, 10000);
</script>
@endsection
