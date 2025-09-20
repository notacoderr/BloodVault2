@extends('layouts.app')

@section('title', 'Edit Appointment - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Appointment
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Please fix the following errors:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('appointment.update', $appointment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="appointment_type" class="form-label">Appointment Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="appointment_type" name="appointment_type" required>
                                    <option value="">Select Type</option>
                                    @foreach($appointmentTypes as $type)
                                        <option value="{{ $type }}" {{ old('appointment_type', $appointment->appointment_type) == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Blood Type (if applicable)</label>
                                <select class="form-select" id="blood_type" name="blood_type">
                                    <option value="">Select Blood Type</option>
                                    <option value="A+" {{ old('blood_type', $appointment->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_type', $appointment->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_type', $appointment->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_type', $appointment->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_type', $appointment->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_type', $appointment->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_type', $appointment->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_type', $appointment->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="appointment_date" class="form-label">Appointment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                       value="{{ old('appointment_date', $appointment->appointment_date) }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="time_slot" class="form-label">Time Slot <span class="text-danger">*</span></label>
                                <select class="form-select" id="time_slot" name="time_slot" required>
                                    <option value="">Select Time</option>
                                    @foreach($timeSlots as $time)
                                        <option value="{{ $time }}" {{ old('time_slot', $appointment->time_slot) == $time ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Any additional information about your appointment...">{{ old('notes', $appointment->notes) }}</textarea>
                        </div>

                        <!-- Current Status Display (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label">Current Status</label>
                            <div class="form-control-plaintext">
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
                                <small class="text-muted ms-2">Status can only be changed by administrators</small>
                            </div>
                        </div>

                        <!-- Admin Notes Display (Read-only) -->
                        @if($appointment->admin_notes)
                        <div class="mb-3">
                            <label class="form-label">Admin Notes</label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ $appointment->admin_notes }}
                                <small class="d-block text-muted mt-1">This note was added by an administrator and cannot be edited</small>
                            </div>
                        </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.appointment-details', $appointment->id) }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to Details
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-check-circle me-2"></i>
                                Update Appointment
                            </button>
                        </div>
                    </form>
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
    
    .form-control-plaintext {
        padding: 0.375rem 0.75rem;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    
    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Updating...');
    });
    
    // Set minimum date for appointment_date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    document.getElementById('appointment_date').setAttribute('min', tomorrowStr);
});
</script>
@endpush
