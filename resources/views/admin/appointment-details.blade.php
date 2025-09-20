@extends('layouts.app')

@section('title', 'Appointment Details - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-calendar-event text-info me-2"></i>
                        Appointment Details
                    </h2>
                    <p class="text-muted mb-0">Review and manage appointment information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.appointments') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-2"></i>Edit Appointment
                    </a>
                </div>
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Appointment Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Appointment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Appointment ID:</label>
                            <p class="mb-0">#{{ $appointment->id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Appointment Type:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $appointment->appointment_type === 'donation' ? 'success' : ($appointment->appointment_type === 'screening' ? 'warning' : 'info') }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->appointment_type)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Date:</label>
                            <p class="mb-0">{{ $appointment->appointment_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Time:</label>
                            <p class="mb-0">{{ $appointment->time_slot }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Status:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $appointment->status === 'pending' ? 'warning' : ($appointment->status === 'confirmed' ? 'info' : ($appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'secondary'))) }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Created:</label>
                            <p class="mb-0">{{ $appointment->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                        @if($appointment->blood_type)
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Blood Type:</label>
                            <p class="mb-0"><span class="badge bg-danger">{{ $appointment->blood_type }}</span></p>
                        </div>
                        @endif
                    </div>
                    
                    @if($appointment->notes)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Notes:</label>
                        <p class="mb-0">{{ $appointment->notes }}</p>
                    </div>
                    @endif
                    
                    @if($appointment->admin_notes)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Admin Notes:</label>
                        <p class="mb-0">{{ $appointment->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Patient Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>
                        Patient Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Name:</label>
                            <p class="mb-0">{{ $appointment->user->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Email:</label>
                            <p class="mb-0">{{ $appointment->user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">User Type:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $appointment->user->usertype === 'admin' ? 'danger' : ($appointment->user->usertype === 'donor' ? 'success' : 'warning') }}">
                                    {{ ucfirst($appointment->user->usertype) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Member Since:</label>
                            <p class="mb-0">{{ $appointment->user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Update Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Update Status
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.appointment-status', $appointment->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">New Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="no-show" {{ $appointment->status == 'no-show' ? 'selected' : '' }}>No Show</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="admin_notes" class="form-label">Admin Notes</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Add notes about this status change...">{{ $appointment->admin_notes }}</textarea>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-2"></i>
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Appointment Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Appointment Created</h6>
                                <p class="text-muted mb-0">{{ $appointment->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @if($appointment->updated_at != $appointment->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Last Updated</h6>
                                <p class="text-muted mb-0">{{ $appointment->updated_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #dee2e6;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: -29px;
    top: 12px;
    width: 2px;
    height: calc(100% + 8px);
    background-color: #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
    color: #495057;
}

.timeline-content p {
    font-size: 0.875rem;
}
</style>
@endpush
