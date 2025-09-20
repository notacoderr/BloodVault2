@extends('layouts.app')

@section('title', 'Blood Donation Details - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-heart text-success me-2"></i>
                        Blood Donation Details
                    </h2>
                    <p class="text-muted mb-0">Review and manage blood donation information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.blood-donations') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#processDonationModal">
                        <i class="bi bi-check-circle me-2"></i>Process Donation
                    </button>
                </div>
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Donation Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Donation Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Donation ID:</label>
                            <p class="mb-0">#{{ $donation->id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Donation Date:</label>
                            <p class="mb-0">{{ $donation->donation_date ? \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') : 'Not specified' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Blood Type:</label>
                            <p class="mb-0"><span class="badge bg-danger">{{ $donation->blood_type }}</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Status:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $donation->status === 'pending' ? 'warning' : ($donation->status === 'approved' ? 'info' : ($donation->status === 'completed' ? 'success' : ($donation->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                    {{ ucfirst($donation->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Screening Completed:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $donation->screening_answers ? 'success' : 'warning' }}">
                                    {{ $donation->screening_answers ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Created:</label>
                            <p class="mb-0">{{ $donation->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($donation->notes)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Notes:</label>
                        <p class="mb-0">{{ $donation->notes }}</p>
                    </div>
                    @endif
                    
                    @if($donation->admin_notes)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Admin Notes:</label>
                        <p class="mb-0">{{ $donation->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Donor Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>
                        Donor Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Name:</label>
                            <p class="mb-0">{{ $donation->user->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Email:</label>
                            <p class="mb-0">{{ $donation->user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">User Type:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $donation->user->usertype === 'admin' ? 'danger' : ($donation->user->usertype === 'donor' ? 'success' : 'warning') }}">
                                    {{ ucfirst($donation->user->usertype) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Member Since:</label>
                            <p class="mb-0">{{ $donation->user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Screening Responses -->
            @if($donation->screening_answers)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-clipboard-check me-2"></i>
                        Screening Responses
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $screeningData = json_decode($donation->screening_answers, true);
                        @endphp
                        @if($screeningData)
                            @foreach($screeningData as $question => $answer)
                            <div class="col-md-6 mb-2">
                                <label class="fw-bold text-muted">{{ ucfirst(str_replace('_', ' ', $question)) }}:</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $answer === 'yes' ? 'danger' : 'success' }}">
                                        {{ ucfirst($answer) }}
                                    </span>
                                </p>
                            </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <p class="text-muted mb-0">No screening responses available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Donation Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Donation Created</h6>
                                <p class="text-muted mb-0">{{ $donation->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @if($donation->updated_at != $donation->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Last Updated</h6>
                                <p class="text-muted mb-0">{{ $donation->updated_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Process Donation Modal -->
<div class="modal fade" id="processDonationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Blood Donation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="processDonationForm">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="screeningStatus" class="form-label">Screening Status</label>
                        <select class="form-select" id="screeningStatus">
                            <option value="">Select Screening Status</option>
                            <option value="pending">Pending</option>
                            <option value="passed">Passed</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity (units)</label>
                        <input type="number" class="form-control" id="quantity" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add any notes about processing this donation..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">General Notes</label>
                        <textarea class="form-control" id="notes" rows="3" placeholder="Add general notes about this donation..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitDonationProcessing()">Process Donation</button>
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

@push('scripts')
<script>
let currentDonationId = {{ $donation->id }};

function submitDonationProcessing() {
    const newStatus = document.getElementById('newStatus').value;
    const screeningStatus = document.getElementById('screeningStatus').value;
    const quantity = document.getElementById('quantity').value;
    const adminNotes = document.getElementById('adminNotes').value;
    const notes = document.getElementById('notes').value;

    if (!newStatus || !quantity) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    fetch(`/admin/blood-donation/${currentDonationId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus,
            screening_status: screeningStatus,
            quantity: quantity,
            admin_notes: adminNotes,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Donation processed successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('processDonationModal')).hide();
            // Reload the page to show updated information
            window.location.reload();
        } else {
            showNotification('Failed to process donation', 'error');
        }
    })
    .catch(error => {
        console.error('Error processing donation:', error);
        showNotification('Error processing donation', 'error');
    });
}

// Set expiration date to 42 days from now (blood expiration)
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form fields if needed
    console.log('Blood donation form initialized');
});

function showNotification(message, type) {
    // Use global notification functions if available, otherwise fallback to console
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
        console.log(`${type}: ${message}`);
        // Fallback: create a simple alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }
}
</script>
@endpush
