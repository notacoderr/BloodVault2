@extends('layouts.app')

@section('title', 'Blood Requests Management - Admin Dashboard')

@push('styles')
<style>
/* Custom styles for blood requests */
</style>
@endpush

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-droplet me-2"></i>
                        Blood Requests Management
                    </h4>
                    <div>
                        <button class="btn btn-light me-2" onclick="exportRequests()">
                            <i class="bi bi-download me-2"></i>
                            Export
                        </button>
                        <button class="btn btn-light" onclick="refreshRequests()">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Refresh
                        </button>
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

                     <!-- Status Overview -->
                     <div class="row mb-4">
                         <div class="col-md-2 mb-3">
                             <div class="card border-0 shadow-sm h-100 text-center">
                                 <div class="card-body p-3">
                                     <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                         <i class="bi bi-clock text-warning" style="font-size: 1.2rem;"></i>
                                     </div>
                                     <h5 class="mb-1" id="pending-count">-</h5>
                                     <p class="text-muted mb-0 small">Pending</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2 mb-3">
                             <div class="card border-0 shadow-sm h-100 text-center">
                                 <div class="card-body p-3">
                                     <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                         <i class="bi bi-check-circle text-info" style="font-size: 1.2rem;"></i>
                                     </div>
                                     <h5 class="mb-1" id="approved-count">-</h5>
                                     <p class="text-muted mb-0 small">Approved</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2 mb-3">
                             <div class="card border-0 shadow-sm h-100 text-center">
                                 <div class="card-body p-3">
                                     <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                         <i class="bi bi-check2-all text-success" style="font-size: 1.2rem;"></i>
                                     </div>
                                     <h5 class="mb-1" id="completed-count">-</h5>
                                     <p class="text-muted mb-0 small">Completed</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2 mb-3">
                             <div class="card border-0 shadow-sm h-100 text-center">
                                 <div class="card-body p-3">
                                     <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                         <i class="bi bi-x-circle text-danger" style="font-size: 1.2rem;"></i>
                                     </div>
                                     <h5 class="mb-1" id="rejected-count">-</h5>
                                     <p class="text-muted mb-0 small">Rejected</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2 mb-3">
                             <div class="card border-0 shadow-sm h-100 text-center">
                                 <div class="card-body p-3">
                                     <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                         <i class="bi bi-x-circle text-secondary" style="font-size: 1.2rem;"></i>
                                     </div>
                                     <h5 class="mb-1" id="cancelled-count">-</h5>
                                     <p class="text-muted mb-0 small">Cancelled</p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-2 mb-3">
                             <div class="card border-0 shadow-sm h-100 text-center">
                                 <div class="card-body p-3">
                                     <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                         <i class="bi bi-list-ul text-primary" style="font-size: 1.2rem;"></i>
                                     </div>
                                     <h5 class="mb-1" id="total-count">-</h5>
                                     <p class="text-muted mb-0 small">Total</p>
                                 </div>
                             </div>
                         </div>
                     </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="completed">Completed</option>
                                <option value="rejected">Rejected</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="urgencyFilter">
                                <option value="">All Urgencies</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="bloodTypeFilter">
                                <option value="">All Blood Types</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search requests..." onkeypress="handleSearchKeyPress(event)">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-warning w-100" onclick="applyFilters()">
                                <i class="bi bi-search me-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- Requests Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Requester</th>
                                    <th>Blood Type</th>
                                    <th>Units</th>
                                    <th>Urgency</th>
                                    <th>Required Date</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="requestsTableBody">
                                <!-- Requests will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4" id="paginationContainer">
                        <!-- Pagination will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Blood Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="requestDetailsContent">
                <!-- Request details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="updateRequestStatus()">
                    <i class="bi bi-pencil-square me-2"></i>Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    Update Request Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Status Update:</strong> Change the request status and add administrative notes below.
                </div>
                <form id="statusUpdateForm">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending - Request is awaiting review</option>
                            <option value="approved">Approved - Request has been approved and blood allocated</option>
                            <option value="completed">Completed - Blood has been delivered/used</option>
                            <option value="rejected">Rejected - Request denied (add reason in notes)</option>
                            <option value="cancelled">Cancelled - Request cancelled by requester</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="allocatedUnits" class="form-label">Allocated Units</label>
                        <input type="number" class="form-control" id="allocatedUnits" min="0" placeholder="Number of units allocated (optional)">
                        <small class="form-text text-muted">Enter the number of blood units allocated for this request. Use when approving requests.</small>
                    </div>
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add any notes about this status change..."></textarea>
                        <small class="form-text text-muted">Required for rejected requests. Useful for approved/completed requests.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitStatusUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};
let currentRequestId = null;

function loadRequests(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page: page,
        ...currentFilters
    });

    // Show loading state
    const tbody = document.getElementById('requestsTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="9" class="text-center py-4">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading requests...</p>
            </td>
        </tr>
    `;

    // Also load status overview counts
    loadStatusOverview();

    fetch(`/admin/blood-requests?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.requests && data.requests.length > 0) {
                displayRequests(data.requests);
                displayPagination(data.pagination);
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">No requests found</p>
                        </td>
                    </tr>
                `;
                displayPagination(null);
            }
        })
        .catch(error => {
            console.error('Error loading requests:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        <p class="mt-2 text-danger">Error loading requests</p>
                        <button class="btn btn-sm btn-outline-warning" onclick="loadRequests(currentPage)">
                            <i class="bi bi-arrow-clockwise me-1"></i>Retry
                        </button>
                    </td>
                </tr>
            `;
            showNotification('Error loading requests. Please try again.', 'error');
        });
}

function displayRequests(requests) {
    const tbody = document.getElementById('requestsTableBody');
    tbody.innerHTML = '';

    requests.forEach(request => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${request.id}</td>
            <td>${request.user ? request.user.name : 'N/A'}</td>
            <td>
                <span class="badge bg-danger">${request.blood_type}</span>
            </td>
            <td>${request.units_needed}</td>
            <td>
                <span class="badge bg-${getUrgencyColor(request.urgency)}">
                    ${request.urgency}
                </span>
            </td>
            <td>${formatDate(request.request_date)}</td>
            <td>
                <span class="badge bg-${getStatusColor(request.status)}">
                    ${request.status}
                </span>
            </td>
            <td>${formatDate(request.created_at)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewRequest(${request.id})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="openStatusUpdate(${request.id})" title="Quick Status Update">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="checkAvailability(${request.id})" title="Check Availability">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function displayPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    if (!pagination) {
        container.innerHTML = '';
        return;
    }

    let html = '<nav><ul class="pagination">';
    
    // Previous button
    if (pagination.prev_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRequests(${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadRequests(${i})">${i}</a></li>`;
    }
    
    // Next button
    if (pagination.next_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRequests(${pagination.current_page + 1})">Next</a></li>`;
    }
    
    html += '</ul></nav>';
    container.innerHTML = html;
}

function applyFilters() {
    currentFilters = {
        status: document.getElementById('statusFilter').value,
        urgency: document.getElementById('urgencyFilter').value,
        blood_type: document.getElementById('bloodTypeFilter').value,
        search: document.getElementById('searchFilter').value
    };
    
    // Remove empty filters
    Object.keys(currentFilters).forEach(key => {
        if (currentFilters[key] === '' || currentFilters[key] === null) {
            delete currentFilters[key];
        }
    });
    
    loadRequests(1);
}

function handleSearchKeyPress(event) {
    if (event.key === 'Enter') {
        applyFilters();
    }
}

function viewRequest(requestId) {
    // Set the current request ID for status updates
    currentRequestId = requestId;
    
    // Show loading state
    document.getElementById('requestDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading request details...</p>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('requestDetailsModal')).show();

    fetch(`/admin/blood-request/${requestId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('requestDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Requester:</strong> ${data.user ? data.user.name : 'N/A'}</p>
                        <p><strong>Email:</strong> ${data.user ? data.user.email : 'N/A'}</p>
                        <p><strong>Blood Type:</strong> <span class="badge bg-danger">${data.blood_type}</span></p>
                        <p><strong>Units Needed:</strong> ${data.units_needed}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Urgency:</strong> <span class="badge bg-${getUrgencyColor(data.urgency)}">${data.urgency}</span></p>
                        <p><strong>Required Date:</strong> ${formatDate(data.request_date)}</p>
                        <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(data.status)}">${data.status}</span></p>
                        <p><strong>Created:</strong> ${formatDate(data.created_at)}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Reason:</strong> ${data.reason || 'N/A'}</p>
                        <p><strong>Hospital/Clinic:</strong> ${data.hospital || 'N/A'}</p>
                        <p><strong>Contact Person:</strong> ${data.contact_person || 'N/A'}</p>
                        <p><strong>Contact Number:</strong> ${data.contact_number || 'N/A'}</p>
                    </div>
                </div>
                ${data.additional_notes ? `<div class="row mt-3"><div class="col-12"><p><strong>Additional Notes:</strong> ${data.additional_notes}</p></div></div>` : ''}
                ${data.admin_notes ? `<div class="row mt-3"><div class="col-12"><p><strong>Admin Notes:</strong> ${data.admin_notes}</p></div></div>` : ''}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Click "Update Status" below to change the request status or add admin notes.
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading request details:', error);
            document.getElementById('requestDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> Failed to load request details. Please try again.
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="viewRequest(${requestId})">
                        <i class="bi bi-arrow-clockwise me-1"></i>Retry
                    </button>
                </div>
            `;
            showNotification('Error loading request details. Please try again.', 'error');
        });
}

function openStatusUpdate(requestId) {
    currentRequestId = requestId;
    document.getElementById('newStatus').value = '';
    document.getElementById('adminNotes').value = '';
    document.getElementById('allocatedUnits').value = '';
    new bootstrap.Modal(document.getElementById('statusUpdateModal')).show();
}

function updateRequestStatus() {
    // This function is called from the modal button
    // It will open the status update modal
    if (currentRequestId) {
        openStatusUpdate(currentRequestId);
    } else {
        showNotification('No request selected for status update', 'error');
    }
}

function submitStatusUpdate() {
    const newStatus = document.getElementById('newStatus').value;
    const adminNotes = document.getElementById('adminNotes').value;
    const allocatedUnits = document.getElementById('allocatedUnits').value;

    if (!newStatus) {
        showNotification('Please select a new status', 'error');
        return;
    }
    
    // Require admin notes for rejected requests
    if (newStatus === 'rejected' && !adminNotes.trim()) {
        showNotification('Admin notes are required when rejecting a request', 'error');
        return;
    }

    // Show loading state
    const submitBtn = document.querySelector('#statusUpdateModal .btn-warning');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
    submitBtn.disabled = true;

    fetch(`/admin/blood-request/${currentRequestId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            status: newStatus,
            admin_notes: adminNotes || '',
            allocated_units: allocatedUnits === '' ? null : allocatedUnits
        })
    })
    .then(response => {
        if (!response.ok) {
            // Try to get error details from response
            return response.json().then(errorData => {
                throw new Error(`HTTP error! status: ${response.status}, message: ${JSON.stringify(errorData)}`);
            }).catch(() => {
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Request status updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
            loadRequests(currentPage);
            // Also refresh the status overview
            loadStatusOverview();
        } else {
            showNotification(data.message || 'Failed to update request status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating request status:', error);
        showNotification('Error updating request status. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function checkAvailability(requestId) {
    // Implementation for checking blood availability
    showNotification('Blood availability check feature coming soon', 'info');
}

function exportRequests() {
    const params = new URLSearchParams(currentFilters);
    window.open(`{{ route('admin.blood-requests.export') }}?${params}`, '_blank');
}

function refreshRequests() {
    loadRequests(currentPage);
}

function loadStatusOverview() {
    // Load status counts for the overview cards
    fetch('/admin/blood-requests/status-counts', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Update the status overview cards
        document.getElementById('pending-count').textContent = data.pending || 0;
        document.getElementById('approved-count').textContent = data.approved || 0;
        document.getElementById('completed-count').textContent = data.completed || 0;
        document.getElementById('rejected-count').textContent = data.rejected || 0;
        document.getElementById('cancelled-count').textContent = data.cancelled || 0;
        document.getElementById('total-count').textContent = data.total || 0;
    })
    .catch(error => {
        console.error('Error loading status overview:', error);
        // Set default values on error
        document.getElementById('pending-count').textContent = '0';
        document.getElementById('approved-count').textContent = '0';
        document.getElementById('completed-count').textContent = '0';
        document.getElementById('rejected-count').textContent = '0';
        document.getElementById('cancelled-count').textContent = '0';
        document.getElementById('total-count').textContent = '0';
    });
}

function getUrgencyColor(urgency) {
    switch(urgency) {
        case 'critical': return 'danger';
        case 'high': return 'warning';
        case 'medium': return 'info';
        case 'low': return 'success';
        default: return 'secondary';
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'pending': return 'warning';
        case 'approved': return 'info';
        case 'completed': return 'success';
        case 'rejected': return 'danger';
        case 'cancelled': return 'secondary';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString();
}

function showNotification(message, type) {
    // Use global notification functions if available
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
        // Enhanced fallback notification system
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        const iconMap = {
            'success': 'bi-check-circle',
            'error': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-triangle',
            'info': 'bi-info-circle'
        };
        
        alertDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi ${iconMap[type] || 'bi-info-circle'} me-2"></i>
                <span class="flex-grow-1">${message}</span>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
        
        // Add click to dismiss functionality
        alertDiv.addEventListener('click', function() {
            this.remove();
        });
    }
}

// Global error handler
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    if (e.error && e.error.message && e.error.message.includes('showSuccess')) {
        console.warn('Notification functions not available, using fallback');
    }
});

// Ensure notification functions are available
if (!window.showSuccess) {
    window.showSuccess = function(message) {
        showNotification(message, 'success');
    };
}

if (!window.showError) {
    window.showError = function(message) {
        showNotification(message, 'error');
    };
}

if (!window.showWarning) {
    window.showWarning = function(message) {
        showNotification(message, 'warning');
    };
}

if (!window.showInfo) {
    window.showInfo = function(message) {
        showNotification(message, 'info');
    };
}

// Load requests on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRequests();
    // Also load status overview
    loadStatusOverview();
});
</script>
@endpush

