@extends('layouts.app')

@section('title', 'Edit Blood Donation - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Blood Donation
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

                    <form action="{{ route('blood-donation.update', $bloodDonation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="donor_name" class="form-label">Donor Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="donor_name" name="donor_name" 
                                       value="{{ old('donor_name', $bloodDonation->donor_name) }}" required maxlength="255">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="donor_email" class="form-label">Donor Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="donor_email" name="donor_email" 
                                       value="{{ old('donor_email', $bloodDonation->donor_email) }}" required maxlength="255">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Blood Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="blood_type" name="blood_type" required>
                                    <option value="">Select Blood Type</option>
                                    @foreach($bloodTypes as $type)
                                        <option value="{{ $type }}" {{ old('blood_type', $bloodDonation->blood_type) == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="donation_date" class="form-label">Donation Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="donation_date" name="donation_date" 
                                       value="{{ old('donation_date', $bloodDonation->donation_date) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Any additional information about your donation...">{{ old('notes', $bloodDonation->notes) }}</textarea>
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
                                    $statusColor = $statusColors[$bloodDonation->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }} fs-6">
                                    {{ ucfirst($bloodDonation->status) }}
                                </span>
                                <small class="text-muted ms-2">Status can only be changed by administrators</small>
                            </div>
                        </div>

                        <!-- Admin Notes Display (Read-only) -->
                        @if($bloodDonation->admin_notes)
                        <div class="mb-3">
                            <label class="form-label">Admin Notes</label>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                {{ $bloodDonation->admin_notes }}
                                <small class="d-block text-muted mt-1">This note was added by an administrator and cannot be edited</small>
                            </div>
                        </div>
                        @endif

                        <!-- Screening Status Display (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label">Health Screening Status</label>
                            <div class="form-control-plaintext">
                                @if($bloodDonation->screening_answers)
                                    <span class="badge bg-success fs-6">Completed</span>
                                    <small class="text-muted ms-2">Health screening has been completed</small>
                                @else
                                    <span class="badge bg-warning fs-6">Pending</span>
                                    <small class="text-muted ms-2">Health screening will be completed during your appointment</small>
                                @endif
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.donation-details', $bloodDonation->id) }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to Details
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>
                                Update Donation
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
    
    // Set minimum date for donation_date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    document.getElementById('donation_date').setAttribute('min', tomorrowStr);
});
</script>
@endpush
