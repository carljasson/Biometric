@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.patients')

@section('content')
<div class="container my-5">
    <!-- Back & Print Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back 
        </a>

    </div>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-file-medical-alt"></i> Medical Record Details</h4>
        </div>
        <div class="card-body p-4">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th><i class="fas fa-user"></i> Patient Name</th>
                      <td>{{ optional($record->user)->name ?? Auth::user()->name ?? 'Unknown' }}</td>

                    </tr>
                    <tr>
                        <th scope="row"><i class="fas fa-tint"></i> Blood Type</th>
                        <td>{{ $record->blood_type ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="fas fa-allergies"></i> Allergies</th>
                        <td>{{ $record->allergies ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="fas fa-pills"></i> Medications</th>
                        <td>{{ $record->medications ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="fas fa-cut"></i> Surgeries</th>
                        <td>{{ $record->surgeries ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="fas fa-notes-medical"></i> Medical History</th>
                        <td>{{ $record->medical_history ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="fas fa-heartbeat"></i> Complications</th>
                        <td>{{ $record->Complications ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="fas fa-calendar-plus"></i> Created At</th>
                        <td>{{ $record->created_at->format('F j, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th scope="row"><i class="fas fa-calendar-check"></i> Last Updated</th>
                        <td>{{ $record->updated_at->format('F j, Y h:i A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
@endsection
