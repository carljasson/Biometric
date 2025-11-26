@extends('layouts.responder')
@section('title', 'Emergency Alerts')

@push('styles')
<style>
.modal, .modal-backdrop { z-index: 10500 !important; }
.modal-dialog, .modal-content { position: relative; z-index: 10600 !important; }
.resolve-btn { position: relative; z-index: 11000 !important; }
.fixed-bottom-nav { z-index: 1000 !important; }
</style>
@endpush

@section('content')
<div class="container mt-4 mb-5">
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">&larr; Back</a>
    </div>

    <h3 class="mb-3">🚨 Emergency Alerts</h3>

    @foreach($alerts as $alert)
    <div class="card mb-3" data-lat="{{ $alert->latitude }}" data-lng="{{ $alert->longitude }}">
        <div class="card-body">
            <h5 class="text-danger fw-bold">🚨 {{ $alert->type }}</h5>
            <p><strong>Status:</strong>
                @if($alert->status === 'Responder_on_the_way')
                    <span class="badge bg-success">Responder On The Way</span>
                @else
                    <span class="badge bg-warning text-dark">{{ ucfirst($alert->status) }}</span>
                @endif
            </p>

            <form id="resolve-form-{{ $alert->id }}" method="POST" action="{{ route('responder.alerts.resolve', $alert->id) }}" style="display:none;">
                @csrf
            </form>
            @if($alert->status !== 'Responder_on_the_way')
            <button type="button" class="btn btn-success btn-sm resolve-btn" data-id="{{ $alert->id }}">✅ Mark as Received</button>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.resolve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                title: 'Mark as Received?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then(result => {
                if(result.isConfirmed){
                    document.getElementById('resolve-form-' + id).submit();
                }
            });
        });
    });
});
</script>
@endpush
