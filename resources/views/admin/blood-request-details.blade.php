@extends('layouts.app')

@section('title', 'Blood Request Details - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-list-check text-danger me-2"></i>
                        Blood Request Details
                    </h2>
                    <p class="text-muted mb-0">Review and manage blood request information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.blood-requests') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="{{ route('admin.blood-requests.edit', $request->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-2"></i>Edit Request
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

            <!-- Request Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Request Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Request ID:</label>
                            <p class="mb-0">#{{ $request->id }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Request Date:</label>
                            <p class="mb-0">{{ $request->request_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Blood Type:</label>
                            <p class="mb-0"><span class="badge bg-danger">{{ $request->blood_type }}</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Units Needed:</label>
                            <p class="mb-0">{{ $request->units_needed }} units</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Urgency Level:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $request->urgency === 'critical' ? 'danger' : ($request->urgency === 'high' ? 'warning' : ($request->urgency === 'medium' ? 'info' : 'success')) }}">
                                    {{ ucfirst($request->urgency) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Status:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'info' : ($request->status === 'completed' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Hospital:</label>
                            <p class="mb-0">{{ $request->hospital }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Contact Person:</label>
                            <p class="mb-0">{{ $request->contact_person }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Contact Number:</label>
                            <p class="mb-0">{{ $request->contact_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Blood Available:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $request->blood_available ? 'success' : 'danger' }}">
                                    {{ $request->blood_available ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Allocated Units:</label>
                            <p class="mb-0">{{ $request->allocated_units }} units</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Reason for Request:</label>
                        <p class="mb-0">{{ $request->reason }}</p>
                    </div>
                    
                    @if($request->additional_notes)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Additional Notes:</label>
                        <p class="mb-0">{{ $request->additional_notes }}</p>
                    </div>
                    @endif
                    
                    @if($request->admin_notes)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Admin Notes:</label>
                        <p class="mb-0">{{ $request->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Requester Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>
                        Requester Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Name:</label>
                            <p class="mb-0">{{ $request->user->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Email:</label>
                            <p class="mb-0">{{ $request->user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">User Type:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $request->user->usertype === 'admin' ? 'danger' : ($request->user->usertype === 'donor' ? 'success' : 'warning') }}">
                                    {{ ucfirst($request->user->usertype) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Member Since:</label>
                            <p class="mb-0">{{ $request->user->created_at->format('M d, Y') }}</p>
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
                    <!-- Inventory Warning Alert -->
                    <div id="inventory-warning" class="alert alert-warning d-none" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <span id="inventory-warning-text"></span>
                    </div>
                    
                    <!-- Inventory Success Alert -->
                    <div id="inventory-success" class="alert alert-success d-none" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <span id="inventory-success-text"></span>
                    </div>
                    
                    <form action="{{ route('admin.blood-request-status', $request->id) }}" method="POST" id="status-update-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">New Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="allocated_units" class="form-label">Allocated Units</label>
                                <input type="number" class="form-control" id="allocated_units" name="allocated_units" 
                                       value="{{ $request->allocated_units }}" min="1" max="100" 
                                       placeholder="Enter allocated units">
                                <small class="text-muted">Required when marking as completed</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="admin_notes" class="form-label">Admin Notes</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                          placeholder="Add notes about this status change...">{{ $request->admin_notes }}</textarea>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning" id="update-status-btn">
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
                        Request Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Request Created</h6>
                                <p class="text-muted mb-0">{{ $request->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @if($request->updated_at != $request->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Last Updated</h6>
                                <p class="text-muted mb-0">{{ $request->updated_at->format('M d, Y \a\t g:i A') }}</p>
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

@push('scripts')
<script>
$(document).ready(function() {
    const statusSelect = $('#status');
    const allocatedUnitsInput = $('#allocated_units');
    const inventoryWarning = $('#inventory-warning');
    const inventorySuccess = $('#inventory-success');
    const updateStatusBtn = $('#update-status-btn');
    const statusUpdateForm = $('#status-update-form');
    
    // Check inventory availability when status changes to completed
    statusSelect.on('change', function() {
        const selectedStatus = $(this).val();
        const requestId = '{{ $request->id }}';
        
        // Hide previous alerts
        inventoryWarning.addClass('d-none');
        inventorySuccess.addClass('d-none');
        
        if (selectedStatus === 'completed') {
            // Show allocated units field as required
            allocatedUnitsInput.prop('required', true);
            allocatedUnitsInput.closest('.mb-3').find('label').addClass('text-danger');
            
            // Check inventory availability
            checkInventoryAvailability(requestId);
        } else {
            // Hide allocated units field as required
            allocatedUnitsInput.prop('required', false);
            allocatedUnitsInput.closest('.mb-3').find('label').removeClass('text-danger');
        }
    });
    
    // Check inventory when allocated units change
    allocatedUnitsInput.on('input', function() {
        const selectedStatus = statusSelect.val();
        if (selectedStatus === 'completed') {
            const requestId = '{{ $request->id }}';
            checkInventoryAvailability(requestId);
        }
    });
    
    // Function to check inventory availability
    function checkInventoryAvailability(requestId) {
        const allocatedUnits = allocatedUnitsInput.val();
        
        if (!allocatedUnits || allocatedUnits <= 0) {
            showInventoryWarning('Please enter allocated units to check inventory availability.');
            return;
        }
        
        // Show loading state
        updateStatusBtn.prop('disabled', true);
        updateStatusBtn.html('<i class="bi bi-hourglass-split me-2"></i>Checking Inventory...');
        
        $.ajax({
            url: `/admin/blood-request/${requestId}/completion-eligibility`,
            method: 'GET',
            success: function(response) {
                if (response.can_complete) {
                    showInventorySuccess(response.message);
                    updateStatusBtn.prop('disabled', false);
                } else {
                    showInventoryWarning(response.message);
                    updateStatusBtn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                showInventoryWarning('Failed to check inventory availability. Please try again.');
                updateStatusBtn.prop('disabled', false);
            }
        });
        
        // Reset button
        updateStatusBtn.html('<i class="bi bi-check-circle me-2"></i>Update Status');
    }
    
    // Function to show inventory warning
    function showInventoryWarning(message) {
        $('#inventory-warning-text').text(message);
        inventoryWarning.removeClass('d-none');
        inventorySuccess.addClass('d-none');
    }
    
    // Function to show inventory success
    function showInventorySuccess(message) {
        $('#inventory-success-text').text(message);
        inventorySuccess.removeClass('d-none');
        inventoryWarning.addClass('d-none');
    }
    
    // Form validation before submission
    statusUpdateForm.on('submit', function(e) {
        const selectedStatus = statusSelect.val();
        const allocatedUnits = allocatedUnitsInput.val();
        
        if (selectedStatus === 'completed') {
            if (!allocatedUnits || allocatedUnits <= 0) {
                e.preventDefault();
                showInventoryWarning('Allocated units are required when marking as completed.');
                allocatedUnitsInput.focus();
                return false;
            }
            
            // Check if we have a recent inventory check
            if (inventoryWarning.hasClass('d-none') && inventorySuccess.hasClass('d-none')) {
                e.preventDefault();
                showInventoryWarning('Please wait for inventory check to complete.');
                return false;
            }
            
            // If there's a warning, prevent submission
            if (!inventoryWarning.hasClass('d-none')) {
                e.preventDefault();
                showInventoryWarning('Cannot complete request due to insufficient inventory.');
                return false;
            }
        }
    });
    
    // Initial check if status is already completed
    if (statusSelect.val() === 'completed') {
        statusSelect.trigger('change');
    }
});
</script>
@endpush
