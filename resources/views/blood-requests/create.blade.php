@extends('layouts.app')

@section('title', 'Request Blood - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-droplet me-2"></i>
                        Request Blood
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

                    <form action="{{ route('blood-request.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Blood Type Required</label>
                                <select class="form-select" id="blood_type" name="blood_type" required>
                                    <option value="">Select Blood Type</option>
                                    <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="units_needed" class="form-label">Units Needed</label>
                                <input type="number" class="form-control" id="units_needed" name="units_needed" value="{{ old('units_needed') }}" min="1" max="10" required>
                                <div class="form-text">Maximum 10 units per request</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="urgency" class="form-label">Urgency Level</label>
                                <select class="form-select" id="urgency" name="urgency" required>
                                    <option value="">Select Urgency</option>
                                    <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }}>Low (1-2 weeks)</option>
                                    <option value="medium" {{ old('urgency') == 'medium' ? 'selected' : '' }}>Medium (3-5 days)</option>
                                    <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>High (1-2 days)</option>
                                    <option value="critical" {{ old('urgency') == 'critical' ? 'selected' : '' }}>Critical (Immediate)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="request_date" class="form-label">Required Date</label>
                                <input type="date" class="form-control" id="request_date" name="request_date" value="{{ old('request_date') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Request</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hospital" class="form-label">Hospital/Clinic</label>
                                <input type="text" class="form-control" id="hospital" name="hospital" value="{{ old('hospital') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="additional_notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="additional_notes" name="additional_notes" rows="2">{{ old('additional_notes') }}</textarea>
                            <div class="form-text">Optional: Any additional information that might help</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-droplet me-2"></i>
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('request_date').min = today;
    
    // Form validation
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Submitting...');
    });
});
</script>
@endpush
