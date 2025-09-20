@extends('layouts.app')

@section('title', 'Appointment Details - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        Appointment Details
                    </h4>
                    <a href="{{ route('user.my-appointments') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Appointments
                    </a>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Appointment Type</label>
                            <div>
                                @php
                                    $typeColors = [
                                        'donation' => 'success',
                                        'screening' => 'warning',
                                        'consultation' => 'info'
                                    ];
                                    $typeColor = $typeColors[$appointment->appointment_type] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $typeColor }} fs-6">
                                    {{ ucfirst($appointment->appointment_type) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'secondary',
                                        'rejected' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$appointment->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }} fs-6">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Appointment Date</label>
                            <div class="fs-6">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Time Slot</label>
                            <div class="fs-6">{{ \Carbon\Carbon::parse($appointment->time_slot)->format('g:i A') }}</div>
                        </div>
                    </div>

                    @if($appointment->blood_type)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Blood Type (if applicable)</label>
                            <div>
                                <span class="badge bg-danger fs-6">{{ $appointment->blood_type }}</span>
                            </div>
                        </div>
                    @endif

                    @if($appointment->notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Additional Notes</label>
                            <div class="fs-6">{{ $appointment->notes }}</div>
                        </div>
                    @endif

                    @if($appointment->admin_notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Admin Notes</label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ $appointment->admin_notes }}
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Booking Date</label>
                            <div class="fs-6">{{ \Carbon\Carbon::parse($appointment->created_at)->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <div class="fs-6">{{ \Carbon\Carbon::parse($appointment->updated_at)->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if(in_array($appointment->status, ['pending', 'confirmed']))
                                <form action="{{ route('user.cancel-appointment', $appointment->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Cancel Appointment
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div>
                            <a href="{{ route('user.my-appointments') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to Appointments
                            </a>
                            @if($appointment->status === 'pending')
                                <a href="{{ route('appointment.edit', $appointment->id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>
                                    Edit Appointment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-label {
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .fs-6 {
        font-size: 1rem !important;
    }
    
    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
</style>
@endpush
