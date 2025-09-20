@extends('layouts.app')

@section('title', 'Edit Blood Request - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Blood Request
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

                    <form action="{{ route('blood-request.update', $bloodRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Blood Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="blood_type" name="blood_type" required>
                                    <option value="">Select Blood Type</option>
                                    @foreach($bloodTypes as $type)
                                        <option value="{{ $type }}" {{ old('blood_type', $bloodRequest->blood_type) == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="units_needed" class="form-label">Units Needed <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="units_needed" name="units_needed" 
                                       value="{{ old('units_needed', $bloodRequest->units_needed) }}" min="1" max="100" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="urgency" class="form-label">Urgency Level <span class="text-danger">*</span></label>
                                <select class="form-select" id="urgency" name="urgency" required>
                                    <option value="">Select Urgency</option>
                                    @foreach($urgencyLevels as $level)
                                        <option value="{{ $level }}" {{ old('urgency', $bloodRequest->urgency) == $level ? 'selected' : '' }}>
                                            {{ ucfirst($level) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="request_date" class="form-label">Required Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="request_date" name="request_date" 
                                       value="{{ old('request_date', $bloodRequest->request_date) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hospital" class="form-label">Hospital/Clinic</label>
                                <input type="text" class="form-control" id="hospital" name="hospital" 
                                       value="{{ old('hospital', $bloodRequest->hospital) }}" maxlength="255">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                       value="{{ old('contact_person', $bloodRequest->contact_person) }}" maxlength="255">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                   value="{{ old('contact_number', $bloodRequest->contact_number) }}" maxlength="20">
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Request <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ old('reason', $bloodRequest->reason) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="additional_notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="additional_notes" name="additional_notes" rows="3" 
                                      placeholder="Any additional information you'd like to provide...">{{ old('additional_notes', $bloodRequest->additional_notes) }}</textarea>
                        </div>

                        <!-- Current Status Display (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label">Current Status</label>
                            <div class="form-control-plaintext">
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
                                <small class="text-muted ms-2">Status can only be changed by administrators</small>
                            </div>
                        </div>

                        <!-- Admin Notes Display (Read-only) -->
                        @if($bloodRequest->admin_notes)
                        <div class="mb-3">
                            <label class="form-label">Admin Notes</label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ $bloodRequest->admin_notes }}
                                <small class="d-block text-muted mt-1">This note was added by an administrator and cannot be edited</small>
                            </div>
                        </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.request-details', $bloodRequest->id) }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to Details
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Update Request
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
    
    // Set minimum date for request_date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('request_date').setAttribute('min', today);
});
</script>
@endpush
