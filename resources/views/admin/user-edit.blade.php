@extends('layouts.app')

@section('title', 'Edit User - Admin Dashboard')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-person-gear me-2"></i>
                            Edit User: {{ $user->name }}
                        </h4>
                        <div>
                            <a href="{{ route('admin.user-details', $user->USER_ID) }}" class="btn btn-light btn-sm">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                            <a href="{{ route('admin.users') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Back to Users
                            </a>
                        </div>
                    </div>
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

                    <form action="{{ route('admin.user.update', $user->USER_ID) }}" method="POST" id="editUserForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Fields marked with * are required. All other fields are optional but recommended for better user experience.
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Please ensure all information is accurate, especially blood type and medical details, as this information is critical for blood donation and medical procedures.
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>Form Validation:</strong> All required fields will be validated before submission. Invalid fields will be highlighted with error messages.
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-arrow-repeat me-2"></i>
                            <strong>AJAX Submission:</strong> This form uses AJAX for smooth submission without page reload. You'll see real-time feedback and validation.
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>User Type Warning:</strong> Changing a user's type to "Admin" grants them full system access. Please ensure this is intentional and authorized.
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-droplet me-2"></i>
                            <strong>Blood Type Warning:</strong> Blood type information is critical for medical procedures. Please verify this information with the user or medical records.
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>Verification Warning:</strong> Account verification status affects user access to system features. Only verify accounts that have been properly validated.
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-person me-2"></i>
                            <strong>Personal Information:</strong> Personal details like DOB, sex, and contact information are important for medical eligibility and emergency communications.
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-geo-alt me-2"></i>
                            <strong>Address Information:</strong> Address details help with regional blood bank coordination, emergency services, and appointment scheduling.
                        </div>
                        
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Form Submission:</strong> After successful submission, you will be redirected to the user details page to review the updated information.
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            <strong>Form Reset:</strong> Use the reset button to restore all fields to their original values if you need to start over.
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-x-circle me-2"></i>
                            <strong>Form Cancel:</strong> Use the cancel button to return to the users list without saving any changes.
                        </div>
                        
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Form Update:</strong> Use the update button to save all changes and redirect to the user details page.
                        </div>
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-person me-2"></i>Basic Information
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    <small class="form-text text-muted">
                                        <i class="bi bi-person me-1"></i>
                                        Full name is used for identification and official records.
                                    </small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    <small class="form-text text-muted">
                                        <i class="bi bi-envelope me-1"></i>
                                        Email is used for account access and important notifications.
                                    </small>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="usertype" class="form-label">User Type *</label>
                                    <select class="form-select @error('usertype') is-invalid @enderror" 
                                            id="usertype" name="usertype" required>
                                        <option value="">Select User Type</option>
                                        <option value="admin" {{ old('usertype', $user->usertype) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="donor" {{ old('usertype', $user->usertype) == 'donor' ? 'selected' : '' }}>Donor</option>
                                        <option value="requester" {{ old('usertype', $user->usertype) == 'requester' ? 'selected' : '' }}>Requester</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Setting user type to "Admin" grants full administrative access to the system.
                                    </small>
                                    @error('usertype')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="bloodtype" class="form-label">Blood Type</label>
                                    <select class="form-select @error('bloodtype') is-invalid @enderror" 
                                            id="bloodtype" name="bloodtype">
                                        <option value="">Select Blood Type</option>
                                        <option value="A+" {{ old('bloodtype', $user->bloodtype) == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('bloodtype', $user->bloodtype) == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('bloodtype', $user->bloodtype) == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('bloodtype', $user->bloodtype) == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('bloodtype', $user->bloodtype) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('bloodtype', $user->bloodtype) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('bloodtype', $user->bloodtype) == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('bloodtype', $user->bloodtype) == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="bi bi-droplet me-1"></i>
                                        Blood type is essential for donors and blood matching purposes.
                                    </small>
                                    @error('bloodtype')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Personal Details -->
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-card-text me-2"></i>Personal Details
                                </h5>
                                
                                <div class="mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('dob') is-invalid @enderror" 
                                           id="dob" name="dob" value="{{ old('dob', $user->dob) }}">
                                    <small class="form-text text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        Date of birth is used to determine eligibility for blood donation and appointments.
                                    </small>
                                    @error('dob')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sex" class="form-label">Sex</label>
                                    <select class="form-select @error('sex') is-invalid @enderror" 
                                            id="sex" name="sex">
                                        <option value="">Select Sex</option>
                                        <option value="male" {{ old('sex', $user->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('sex', $user->sex) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('sex', $user->sex) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="bi bi-person me-1"></i>
                                        Sex information is important for medical screening and eligibility requirements.
                                    </small>
                                    @error('sex')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="contact" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control @error('contact') is-invalid @enderror" 
                                           id="contact" name="contact" value="{{ old('contact', $user->contact) }}">
                                    <small class="form-text text-muted">
                                        <i class="bi bi-telephone me-1"></i>
                                        Contact number is used for urgent communications and appointment confirmations.
                                    </small>
                                    @error('contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                                    <small class="form-text text-muted">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        Address information helps with location-based services and emergency contacts.
                                    </small>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" value="{{ old('city', $user->city) }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="province" class="form-label">Province</label>
                                            <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                                   id="province" name="province" value="{{ old('province', $user->province) }}">
                                            @error('province')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    City and province information helps with regional blood bank coordination and emergency services.
                                </small>
                            </div>
                        </div>

                        <!-- Account Status -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-shield-check me-2"></i>Account Status
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="is_verified" name="is_verified" value="1" 
                                                   {{ old('is_verified', $user->is_verified) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_verified">
                                                Account Verified
                                            </label>
                                            <small class="form-text text-muted d-block">
                                                <i class="bi bi-shield-check me-1"></i>
                                                Verified accounts can access all features. Unverified accounts have limited access.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Admin Status:</strong> Set user type to "admin" above to grant administrative privileges.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>Ready to Update:</strong> Click "Update User" to save all changes. You will be redirected to the user details page after successful update.
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Form
                                        </button>
                                        <small class="form-text text-muted d-block mt-1">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Reset will restore all fields to their original values.
                                        </small>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-x-circle me-2"></i>Cancel
                                        </a>
                                        <small class="form-text text-muted d-block mt-1">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Cancel will return to the users list without saving changes.
                                        </small>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="bi bi-check-circle me-2"></i>Update User
                                        </button>
                                        <small class="form-text text-muted d-block mt-1">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Update will save all changes and redirect to user details.
                                        </small>
                                    </div>
                                </div>
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
    const form = document.getElementById('editUserForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
        
        // Submit form
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Redirect to user details page after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("admin.user-details", $user->USER_ID) }}';
                }, 1500);
            } else {
                showNotification(data.message || 'Failed to update user', 'error');
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Update User';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while updating the user', 'error');
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Update User';
        });
    });
});

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All changes will be lost.')) {
        document.getElementById('editUserForm').reset();
        // Reset checkbox to its original state
        document.getElementById('is_verified').checked = {{ $user->is_verified ? 'true' : 'false' }};
    }
}

function showNotification(message, type) {
    if (window.showSuccess && window.showError && window.showWarning && window.showInfo) {
        switch(type) {
            case 'success':
                window.showSuccess(message);
                break;
            case 'error':
                window.showError(message);
                break;
            case 'warning':
                window.showWarning(message);
                break;
            default:
                window.showInfo(message);
        }
    } else {
        // Fallback notification
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}
</script>
@endpush
