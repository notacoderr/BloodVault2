@extends('layouts.app')

@section('title', 'My Appointments - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        My Appointments
                    </h4>
                    <a href="{{ route('appointment.create') }}" class="btn btn-light">
                        <i class="bi bi-plus-circle me-2"></i>
                        Book Appointment
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

                    @if($appointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $appointment)
                                    <tr>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'donation' => 'success',
                                                    'screening' => 'warning',
                                                    'consultation' => 'info'
                                                ];
                                                $typeColor = $typeColors[$appointment->appointment_type] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $typeColor }}">
                                                {{ ucfirst($appointment->appointment_type) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->time_slot)->format('g:i A') }}</td>
                                        <td>
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
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ ucfirst($appointment->status) }}
                                            </span>
                                            @if($appointment->status === 'rejected' && $appointment->admin_notes)
                                                <div class="mt-1">
                                                    <small class="text-danger">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        <strong>Reason:</strong> {{ $appointment->admin_notes }}
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->created_at)->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.appointment-details', $appointment->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(in_array($appointment->status, ['pending', 'confirmed']))
                                                    <form action="{{ route('user.cancel-appointment', $appointment->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Cancel Appointment">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $appointments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-event text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No Appointments Yet</h5>
                            <p class="text-muted">You haven't booked any appointments yet.</p>
                            <a href="{{ route('appointment.create') }}" class="btn btn-info">
                                <i class="bi bi-plus-circle me-2"></i>
                                Book Your First Appointment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .badge {
        font-size: 0.8rem;
    }
</style>
@endpush
