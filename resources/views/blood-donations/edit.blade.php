@extends('layouts.app')

@section('title', 'Edit Blood Donation - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
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
                            <strong>Please fix the following errors:</strong>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="donor_name" class="form-label">Donor Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('donor_name') is-invalid @enderror" 
                                           id="donor_name" name="donor_name" 
                                           value="{{ old('donor_name', $bloodDonation->donor_name) }}" required>
                                    @error('donor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="donor_email" class="form-label">Donor Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('donor_email') is-invalid @enderror" 
                                           id="donor_email" name="donor_email" 
                                           value="{{ old('donor_email', $bloodDonation->donor_email) }}" required>
                                    @error('donor_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blood_type" class="form-label">Blood Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('blood_type') is-invalid @enderror" 
                                            id="blood_type" name="blood_type" required>
                                        <option value="">Select Blood Type</option>
                                        @foreach($bloodTypes as $type)
                                            <option value="{{ $type }}" {{ old('blood_type', $bloodDonation->blood_type) == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('blood_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="donation_date" class="form-label">Donation Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('donation_date') is-invalid @enderror" 
                                           id="donation_date" name="donation_date" 
                                           value="{{ old('donation_date', $bloodDonation->donation_date) }}" required>
                                    @error('donation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Any additional information about your donation...">{{ old('notes', $bloodDonation->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> You can only edit pending blood donations. Once a donation is approved or completed, it cannot be modified.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.my-donations') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to My Donations
                            </a>
                            <div>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Update Donation
                                </button>
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    document.getElementById('donation_date').min = tomorrowStr;
});
</script>
@endpush
