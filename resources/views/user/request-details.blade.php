@extends('layouts.app')

@section('title', 'Request Details - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-droplet me-2"></i>
                        Blood Request Details
                    </h4>
                    <a href="{{ route('user.my-requests') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Requests
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
                            <label class="form-label fw-bold">Blood Type Required</label>
                            <div>
                                <span class="badge bg-danger fs-6">{{ $bloodRequest->blood_type }}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Units Needed</label>
                            <div class="fs-6">{{ $bloodRequest->units_needed }} units</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Urgency Level</label>
                            <div>
                                @php
                                    $urgencyColors = [
                                        'low' => 'success',
                                        'medium' => 'warning',
                                        'high' => 'danger',
                                        'critical' => 'dark'
                                    ];
                                    $urgencyColor = $urgencyColors[$bloodRequest->urgency] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $urgencyColor }} fs-6">
                                    {{ ucfirst($bloodRequest->urgency) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Required Date</label>
                            <div class="fs-6">{{ \Carbon\Carbon::parse($bloodRequest->request_date)->format('F d, Y') }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Request</label>
                        <div class="fs-6">{{ $bloodRequest->reason }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hospital/Clinic</label>
                            <div class="fs-6">{{ $bloodRequest->hospital }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contact Person</label>
                            <div class="fs-6">{{ $bloodRequest->contact_person }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Contact Number</label>
                        <div class="fs-6">{{ $bloodRequest->contact_number }}</div>
                    </div>

                    @if($bloodRequest->additional_notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Additional Notes</label>
                            <div class="fs-6">{{ $bloodRequest->additional_notes }}</div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'secondary',
                                        'rejected' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$bloodRequest->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }} fs-6">
                                    {{ ucfirst($bloodRequest->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Request Date</label>
                            <div class="fs-6">{{ \Carbon\Carbon::parse($bloodRequest->created_at)->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>

                    @if($bloodRequest->admin_notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Admin Notes</label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ $bloodRequest->admin_notes }}
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($bloodRequest->status === 'pending')
                                <form action="{{ route('user.cancel-request', $bloodRequest->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to cancel this request?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Cancel Request
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div>
                            <a href="{{ route('user.my-requests') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to Requests
                            </a>
                            @if($bloodRequest->status === 'pending')
                                <a href="{{ route('blood-request.edit', $bloodRequest->id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>
                                    Edit Request
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
