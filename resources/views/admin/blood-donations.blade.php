@extends('layouts.app')

@section('title', 'Blood Donations Management - Admin Dashboard')

@push('styles')
<style>
/* Custom styles for blood donations */
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
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-heart me-2"></i>
                        Blood Donations Management
                    </h4>
                    <div>
                        <button class="btn btn-light me-2" onclick="exportDonations()">
                            <i class="bi bi-download me-2"></i>
                            Export
                        </button>
                        <button class="btn btn-light" onclick="refreshDonations()">
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
                        <div class="col-md-2">
                            <select class="form-select" id="screeningFilter">
                                <option value="">All Screening</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search donations..." onkeypress="handleSearchKeyPress(event)">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-success w-100" onclick="applyFilters()">
                                <i class="bi bi-search me-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- Donations Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Donor</th>
                                    <th>Blood Type</th>
                                    <th>Donation Date</th>
                                    <th>Status</th>
                                    <th>Screening</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="donationsTableBody">
                                <!-- Donations will be loaded here via AJAX -->
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

<!-- Donation Details Modal -->
<div class="modal fade" id="donationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Blood Donation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="donationDetailsContent">
                <!-- Donation details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="updateDonationStatus()">
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
                <h5 class="modal-title">Update Donation Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add any notes about this donation..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitStatusUpdate()">
                    <i class="bi bi-pencil-square me-2"></i>Update Status
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};
let currentDonationId = null;

function loadDonations(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page: page,
        ...currentFilters
    });

    // Show loading state
    const tbody = document.getElementById('donationsTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading donations...</p>
            </td>
        </tr>
    `;

    // Also load status overview counts
    loadStatusOverview();

    fetch(`/admin/blood-donations?${params}`, {
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
            if (data.donations && data.donations.length > 0) {
                displayDonations(data.donations);
                displayPagination(data.pagination);
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">No donations found</p>
                        </td>
                    </tr>
                `;
                displayPagination(null);
            }
        })
        .catch(error => {
            console.error('Error loading donations:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        <p class="mt-2 text-danger">Error loading donations</p>
                        <button class="btn btn-sm btn-outline-success" onclick="loadDonations(currentPage)">
                            <i class="bi bi-arrow-clockwise me-1"></i>Retry
                        </button>
                    </td>
                </tr>
            `;
            showNotification('Error loading donations. Please try again.', 'error');
        });
}

function displayDonations(donations) {
    const tbody = document.getElementById('donationsTableBody');
    tbody.innerHTML = '';

    donations.forEach(donation => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${donation.id}</td>
            <td>${donation.user ? donation.user.name : donation.donor_name || 'N/A'}</td>
            <td>
                <span class="badge bg-success">${donation.blood_type}</span>
            </td>
            <td>${formatDate(donation.donation_date)}</td>
            <td>
                <span class="badge bg-${getStatusColor(donation.status)}">
                    ${donation.status}
                </span>
            </td>
            <td>
                <span class="badge bg-${donation.screening_answers ? 'info' : 'warning'}">
                    ${donation.screening_answers ? 'Completed' : 'Pending'}
                </span>
            </td>
            <td>${formatDate(donation.created_at)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewDonation(${donation.id})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="openStatusUpdate(${donation.id})" title="Update Status">
                        <i class="bi bi-pencil-square"></i>
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
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadDonations(${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadDonations(${i})">${i}</a></li>`;
    }
    
    // Next button
    if (pagination.next_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadDonations(${pagination.current_page + 1})">Next</a></li>`;
    }
    
    html += '</ul></nav>';
    container.innerHTML = html;
}

function applyFilters() {
    currentFilters = {
        status: document.getElementById('statusFilter').value,
        blood_type: document.getElementById('bloodTypeFilter').value,
        screening: document.getElementById('screeningFilter').value,
        search: document.getElementById('searchFilter').value
    };
    
    // Remove empty filters
    Object.keys(currentFilters).forEach(key => {
        if (currentFilters[key] === '' || currentFilters[key] === null) {
            delete currentFilters[key];
        }
    });
    
    loadDonations(1);
}

function handleSearchKeyPress(event) {
    if (event.key === 'Enter') {
        applyFilters();
    }
}

function viewDonation(donationId) {
    // Set the current donation ID for status updates
    currentDonationId = donationId;
    
    // Show loading state
    document.getElementById('donationDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading donation details...</p>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('donationDetailsModal')).show();

    fetch(`/admin/blood-donation/${donationId}`, {
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
            let screeningHtml = '';
            if (data.screening_answers) {
                try {
                    const screeningData = JSON.parse(data.screening_answers);
                    screeningHtml = '<div class="row mt-3"><div class="col-12"><h6>Screening Responses:</h6><div class="card bg-light"><div class="card-body">';
                    Object.entries(screeningData).forEach(([key, value]) => {
                        const question = getScreeningQuestion(key);
                        screeningHtml += `<div class="row mb-2"><div class="col-md-8"><small class="text-muted">${question}</small></div><div class="col-md-4"><span class="badge bg-${value === 'yes' ? 'danger' : 'success'}">${value}</span></div></div>`;
                    });
                    screeningHtml += '</div></div></div>';
                } catch (e) {
                    screeningHtml = '<div class="row mt-3"><div class="col-12"><p class="text-muted">Screening data available but format error</p></div></div>';
                }
            }

            document.getElementById('donationDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Donor:</strong> ${data.user ? data.user.name : data.donor_name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${data.user ? data.user.email : data.donor_email || 'N/A'}</p>
                        <p><strong>Blood Type:</strong> <span class="badge bg-success">${data.blood_type}</span></p>
                        <p><strong>Donation Date:</strong> ${formatDate(data.donation_date)}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(data.status)}">${data.status}</span></p>
                        <p><strong>Screening:</strong> <span class="badge bg-${data.screening_answers ? 'info' : 'warning'}">${data.screening_answers ? 'Completed' : 'Pending'}</span></p>
                        <p><strong>Created:</strong> ${formatDate(data.created_at)}</p>
                        <p><strong>Notes:</strong> ${data.notes || 'N/A'}</p>
                    </div>
                </div>
                ${screeningHtml}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Click "Update Status" below to change the donation status or add admin notes.
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading donation details:', error);
            document.getElementById('donationDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> Failed to load donation details. Please try again.
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="viewDonation(${donationId})">
                        <i class="bi bi-arrow-clockwise me-1"></i>Retry
                    </button>
                </div>
            `;
            showNotification('Error loading donation details. Please try again.', 'error');
        });
}

function openStatusUpdate(donationId) {
    currentDonationId = donationId;
    document.getElementById('newStatus').value = '';
    document.getElementById('adminNotes').value = '';
    new bootstrap.Modal(document.getElementById('statusUpdateModal')).show();
}

function updateDonationStatus() {
    // This function is called from the modal button
    // It will open the status update modal
    if (currentDonationId) {
        openStatusUpdate(currentDonationId);
    } else {
        showNotification('No donation selected for status update', 'error');
    }
}

function submitStatusUpdate() {
    const newStatus = document.getElementById('newStatus').value;
    const adminNotes = document.getElementById('adminNotes').value;

    if (!newStatus) {
        showNotification('Please select a new status', 'error');
        return;
    }
    
    // Require admin notes for rejected donations
    if (newStatus === 'rejected' && !adminNotes.trim()) {
        showNotification('Admin notes are required when rejecting a donation', 'error');
        return;
    }

    // Show loading state
    const submitBtn = document.querySelector('#statusUpdateModal .btn-warning');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
    submitBtn.disabled = true;

    fetch(`/admin/blood-donation/${currentDonationId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            status: newStatus,
            notes: adminNotes || ''
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Donation status updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
            loadDonations(currentPage);
            // Also refresh the status overview
            loadStatusOverview();
        } else {
            showNotification(data.message || 'Failed to update donation status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating donation status:', error);
        showNotification('Error updating donation status. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function exportDonations() {
    const params = new URLSearchParams(currentFilters);
    window.open(`{{ route('admin.blood-donations.export') }}?${params}`, '_blank');
}

function refreshDonations() {
    // Clear current filters and reload
    currentFilters = {};
    document.getElementById('statusFilter').value = '';
    document.getElementById('bloodTypeFilter').value = '';
    document.getElementById('screeningFilter').value = '';
    document.getElementById('searchFilter').value = '';
    
    // Reload donations and status overview
    loadDonations(1);
    loadStatusOverview();
}

function loadStatusOverview() {
    // Load status counts for the overview cards
    fetch('/admin/blood-donations/status-counts', {
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

function getScreeningQuestion(key) {
    const questions = {
        'feels_healthy': 'Feeling healthy and well today?',
        'taking_antibiotic': 'Currently taking an antibiotic?',
        'medfor_infection': 'Currently taking medication for infection?',
        'currently_pregnant': 'Pregnant now?',
        'took_aspirin': 'Taken aspirin in past 48 hours?',
        'donated_blood': 'Donated blood in past 8 weeks?',
        'had_vaccine': 'Had vaccinations in past 8 weeks?',
        'contact_w_smallpox': 'Contact with smallpox vaccine recipients?',
        'med_for_hiv': 'Taken HIV prevention medication?',
        'sex_new_partner': 'Sexual contact with new partner?',
        'sex_more_partner': 'Sexual contact with multiple partners?',
        'sex_hiv_positive': 'Sexual contact with HIV+ partner?',
        'sex_get_payment': 'Received payment for sex?',
        'sex_partner_get_payment': 'Partner received payment for sex?',
        'used_injected_drugs': 'Used injection drugs?',
        'sex_partner_used_injected_drugs': 'Partner used injection drugs?',
        'had_syphilis_gonorrhea': 'Had syphilis or gonorrhea?',
        'sex_partner_has_hepatitis': 'Sexual contact with hepatitis patient?',
        'live_with_hepatitis_patient': 'Lived with hepatitis patient?',
        'accidental_needle_stick': 'Had accidental needle stick?',
        'contact_with_others_blood': 'Contact with others\' blood?',
        'had_tattoo': 'Had tattoo?',
        'had_piercing': 'Had piercing?',
        'had_transfusion': 'Had blood transfusion?',
        'had_transplant': 'Had organ transplant?',
        'had_graft': 'Had tissue graft?'
    };
    return questions[key] || key;
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

// Load donations on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDonations();
    // Also load status overview
    loadStatusOverview();
});
</script>
@endpush

