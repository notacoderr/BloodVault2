@extends('layouts.app')

@section('title', 'User Management - Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        User Management
                    </h4>
                    <div>
                        <button class="btn btn-light me-2" onclick="exportUsers()">
                            <i class="bi bi-download me-2"></i>
                            Export
                        </button>
                        <button class="btn btn-light" onclick="refreshUsers()">
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

                    <!-- User Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Active Users</h5>
                                    <p class="card-text" id="activeUsersCount">Loading...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Deactivated Users</h5>
                                    <p class="card-text" id="deactivatedUsersCount">Loading...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Users</h5>
                                    <p class="card-text" id="totalUsersCount">Loading...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Admin Users</h5>
                                    <p class="card-text" id="adminUsersCount">Loading...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                                                    <select class="form-select" id="statusFilter">
                            <option value="">All Statuses</option>
                            <option value="1">Active Users</option>
                            <option value="0">Deactivated Users</option>
                        </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="usertypeFilter">
                                <option value="">All User Types</option>
                                <option value="admin">Admin</option>
                                <option value="donor">Donor</option>
                                <option value="requester">Requester</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search users...">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="bi bi-search me-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>User Type</th>
                                    <th>Blood Type</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <!-- Users will be loaded here via AJAX -->
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

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- User details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="/admin/user/${currentUserId}/edit" class="btn btn-success">
                    <i class="bi bi-pencil me-2"></i>Edit User
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};
let currentUserId = null;

function loadUserStats() {
    fetch('/admin/users/stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('activeUsersCount').textContent = data.active_users || 0;
        document.getElementById('deactivatedUsersCount').textContent = data.deactivated_users || 0;
        document.getElementById('totalUsersCount').textContent = data.total_users || 0;
        document.getElementById('adminUsersCount').textContent = data.admin_users || 0;
    })
    .catch(error => {
        console.error('Error loading user stats:', error);
    });
}

function loadUsers(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page: page,
        ...currentFilters
    });

    // Show loading state
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading users...</p>
            </td>
        </tr>
    `;

    fetch(`/admin/users?${params}`, {
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
            if (data.users && data.users.length > 0) {
                displayUsers(data.users);
                displayPagination(data.pagination);
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">No users found</p>
                        </td>
                    </tr>
                `;
                displayPagination(null);
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        <p class="mt-2 text-danger">Error loading users</p>
                        <button class="btn btn-sm btn-outline-primary" onclick="loadUsers(currentPage)">
                            <i class="bi bi-arrow-clockwise me-1"></i>Retry
                        </button>
                    </td>
                </tr>
            `;
            showNotification('Error loading users. Please try again.', 'error');
        });
}

function displayUsers(users) {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '';

    users.forEach(user => {
        const row = document.createElement('tr');
        // Add visual styling for deactivated users
        if (!user.is_verified) {
            row.className = 'table-danger';
            row.style.opacity = '0.7';
        }
        row.innerHTML = `
            <td>${user.USER_ID}</td>
            <td>${user.name || 'N/A'}</td>
            <td>${user.email}</td>
            <td>
                <span class="badge bg-${getUserTypeColor(user.usertype)}">
                    ${user.usertype || 'N/A'}
                </span>
            </td>
            <td>
                ${user.bloodtype ? `<span class="badge bg-danger">${user.bloodtype}</span>` : 'N/A'}
            </td>
            <td>
                <div class="d-flex flex-column align-items-start">
                    <span class="badge bg-${getStatusColor(user.is_verified)} fs-6">
                        <i class="bi bi-${user.is_verified ? 'check-circle' : 'x-circle'} me-1"></i>
                        ${user.is_verified ? 'Active' : 'DEACTIVATED'}
                    </span>
                    <small class="text-muted mt-1">
                        ${user.is_verified ? 'Account Verified' : 'Account Deactivated'}
                    </small>
                </div>
            </td>
            <td>${formatDate(user.created_at)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewUser(${user.USER_ID})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    <a href="/admin/user/${user.USER_ID}/edit" class="btn btn-sm btn-outline-success" title="Edit User">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-warning" onclick="toggleUserStatus(${user.USER_ID}, ${user.is_verified})" title="Toggle Status">
                        <i class="bi bi-toggle-${user.is_verified ? 'on' : 'off'}"></i>
                        ${user.is_verified ? 'Deactivate' : 'Activate'}
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.USER_ID})" title="Delete User">
                        <i class="bi bi-trash"></i>
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
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadUsers(${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadUsers(${i})">${i}</a></li>`;
    }
    
    // Next button
    if (pagination.next_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadUsers(${pagination.current_page + 1})">Next</a></li>`;
    }
    
    html += '</ul></nav>';
    container.innerHTML = html;
}

function applyFilters() {
    currentFilters = {
        status: document.getElementById('statusFilter').value,
        usertype: document.getElementById('usertypeFilter').value,
        search: document.getElementById('searchFilter').value
    };
    loadUsers(1);
}

function viewUser(userId) {
    currentUserId = userId;
    
    // Show loading state
    document.getElementById('userDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading user details...</p>
        </div>
    `;
    
    // Show modal first
    new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
    
    fetch(`/admin/user/${userId}`, {
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
            document.getElementById('userDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> ${data.name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${data.email}</p>
                        <p><strong>User Type:</strong> <span class="badge bg-${getUserTypeColor(data.usertype)}">${data.usertype || 'N/A'}</span></p>
                        <p><strong>Blood Type:</strong> ${data.bloodtype ? `<span class="badge bg-danger">${data.bloodtype}</span>` : 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date of Birth:</strong> ${data.dob || 'N/A'}</p>
                        <p><strong>Sex:</strong> ${data.sex || 'N/A'}</p>
                        <p><strong>Contact:</strong> ${data.contact || 'N/A'}</p>
                        <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(data.is_verified)}">${data.is_verified ? 'Active' : 'Inactive'}</span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Address:</strong> ${data.address || 'N/A'}</p>
                        <p><strong>City:</strong> ${data.city || 'N/A'}</p>
                        <p><strong>Province:</strong> ${data.province || 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">Blood Requests</h6>
                            <span class="badge bg-info fs-5">${data.blood_requests ? data.blood_requests.length : 0}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">Blood Donations</h6>
                            <span class="badge bg-success fs-5">${data.blood_donations ? data.blood_donations.length : 0}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 class="text-muted">Appointments</h6>
                            <span class="badge bg-warning fs-5">${data.appointments ? data.appointments.length : 0}</span>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading user details:', error);
            document.getElementById('userDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> Failed to load user details. Please try again.
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="viewUser(${userId})">
                        <i class="bi bi-arrow-clockwise me-1"></i>Retry
                    </button>
                </div>
            `;
            showNotification('Error loading user details. Please try again.', 'error');
        });
}

function toggleUserStatus(userId, currentStatus) {
    const newStatus = !currentStatus;
    const actionText = newStatus ? 'Activate' : 'Deactivate';
    const iconClass = newStatus ? 'toggle-on' : 'toggle-off';

    if (confirm(`Are you sure you want to ${actionText} this user?`)) {
        // Find the button that was clicked and show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Updating...';
        fetch(`/admin/user/${userId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            if (data.success) {
                showNotification(`User ${actionText} successfully`, 'success');
                loadUsers(currentPage);
                loadUserStats();
            } else {
                showNotification(data.message || `Failed to ${actionText} user`, 'error');
            }
        })
        .catch(error => {
            console.error('Error updating user status:', error);
            showNotification(`Error updating user status: ${error.message}`, 'error');
        })
        .finally(() => {
            // Reset button state
            button.disabled = false;
            button.innerHTML = originalHTML;
        });
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        // Find the button that was clicked and show loading state
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Deleting...';
        fetch(`/admin/user/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            if (data.success) {
                showNotification('User deleted successfully', 'success');
                loadUsers(currentPage);
            } else {
                showNotification(data.message || 'Failed to delete user', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            showNotification(`Error deleting user: ${error.message}`, 'error');
        })
        .finally(() => {
            // Reset button state
            button.disabled = false;
            button.innerHTML = originalHTML;
        });
    }
}

function exportUsers() {
    const params = new URLSearchParams(currentFilters);
    window.open(`{{ route('admin.users.export') }}?${params}`, '_blank');
}

function refreshUsers() {
    loadUsers(currentPage);
    loadUserStats();
}

function getUserTypeColor(usertype) {
    switch(usertype) {
        case 'admin': return 'danger';
        case 'donor': return 'success';
        case 'requester': return 'warning';
        default: return 'secondary';
    }
}

function getStatusColor(isVerified) {
    return isVerified ? 'success' : 'danger';
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString();
}

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
        
        const container = document.querySelector('.container-fluid') || document.querySelector('.container');
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

// Load users on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    loadUserStats();
});
</script>
@endpush


