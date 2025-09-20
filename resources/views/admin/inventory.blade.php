@extends('layouts.app')

@section('title', 'Blood Inventory Management - Admin Dashboard')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-box-seam me-2"></i>
                        Blood Inventory Management
                    </h4>
                    <div>
                        <button class="btn btn-light me-2" onclick="exportInventory()">
                            <i class="bi bi-download me-2"></i>
                            Export
                        </button>
                        <button class="btn btn-light" onclick="refreshInventory()">
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

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Low Stock</h5>
                                    <p class="card-text" id="lowStockCount">Loading...</p>
                                    <button class="btn btn-light btn-sm" onclick="viewLowStock()">View Details</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Expiring Soon</h5>
                                    <p class="card-text" id="expiringCount">Loading...</p>
                                    <button class="btn btn-light btn-sm" onclick="viewExpiring()">View Details</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Expired</h5>
                                    <p class="card-text" id="expiredCount">Loading...</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button class="btn btn-light btn-sm" onclick="viewExpired()">View Details</button>
                                        <button class="btn btn-outline-light btn-sm" onclick="removeAllExpired()">Remove All</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Units</h5>
                                    <p class="card-text" id="totalUnits">Loading...</p>
                                    <button class="btn btn-light btn-sm" onclick="viewTotal()">View Details</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="1">Available</option>
                                <option value="0">Unavailable</option>
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
                            <select class="form-select" id="expirationFilter">
                                <option value="">All Expirations</option>
                                <option value="7">Expires in 7 days</option>
                                <option value="14">Expires in 14 days</option>
                                <option value="30">Expires in 30 days</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search inventory...">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-danger w-100" onclick="applyFilters()">
                                <i class="bi bi-search me-2"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Donor</th>
                                    <th>Blood Type</th>
                                    <th>Quantity</th>
                                    <th>Acquisition Date</th>
                                    <th>Expiration Date</th>
                                    <th>Status</th>
                                    <th>Days Left</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                <!-- Inventory items will be loaded here via AJAX -->
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

<!-- Inventory Details Modal -->
<div class="modal fade" id="inventoryDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Blood Inventory Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="inventoryDetailsContent">
                <!-- Inventory details will be loaded here -->
            </div>
                            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="updateInventoryStatus()">
                    <i class="bi bi-pencil me-2"></i>Update Status
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
                <h5 class="modal-title">Update Inventory Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="">Select Status</option>
                            <option value="1">Available</option>
                            <option value="0">Unavailable</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add any notes about this status change..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitStatusUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};
let currentInventoryId = null;

function loadInventory(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page: page,
        ...currentFilters
    });

    fetch(`/admin/inventory/list?${params}`)
        .then(response => response.json())
        .then(data => {
            displayInventory(data.inventory);
            displayPagination(data.pagination);
        })
        .catch(error => {
            console.error('Error loading inventory:', error);
            showNotification('Error loading inventory', 'error');
        });
}

function loadInventoryStats() {
    fetch('/admin/inventory/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('lowStockCount').textContent = data.low_stock || 0;
            document.getElementById('expiringCount').textContent = data.expiring_soon || 0;
            document.getElementById('expiredCount').textContent = data.expired || 0;
            document.getElementById('totalUnits').textContent = data.total_units || 0;
        })
        .catch(error => {
            console.error('Error loading inventory stats:', error);
        });
}

function displayInventory(inventory) {
    const tbody = document.getElementById('inventoryTableBody');
    tbody.innerHTML = '';

    inventory.forEach(item => {
        const daysLeft = calculateDaysLeft(item.expiration_date);
        const row = document.createElement('tr');
        row.className = daysLeft <= 0 ? 'table-danger' : daysLeft <= 7 ? 'table-warning' : '';
        
        // Debug: Log the donor data
        console.log('Donor data for item:', item.STOCK_ID, item.donor, 'Donor field:', item.donor, 'Full item:', item);
        
        // Check if donor exists and has a name, otherwise show Anonymous
        let donorName = 'Anonymous Donor';
        if (item.donor && item.donor.name) {
            donorName = item.donor.name;
        } else if (item.donor) {
            donorName = `User ID: ${item.donor}`;
        }
        
        row.innerHTML = `
            <td>${item.STOCK_ID}</td>
            <td>${donorName}</td>
            <td>
                <span class="badge bg-danger">${item.blood_type}</span>
            </td>
            <td>${item.quantity}</td>
            <td>${formatDate(item.acquisition_date)}</td>
            <td>${formatDate(item.expiration_date)}</td>
            <td>
                <span class="badge bg-${item.status ? 'success' : 'secondary'}">
                    ${item.status ? 'Available' : 'Unavailable'}
                </span>
            </td>
            <td>
                <span class="badge bg-${getDaysLeftColor(daysLeft)}">
                    ${daysLeft > 0 ? `${daysLeft} days` : 'Expired'}
                </span>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewInventory(${item.STOCK_ID})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="openStatusUpdate(${item.STOCK_ID})" title="Update Status">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="removeExpired(${item.STOCK_ID})" title="Remove Expired">
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
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadInventory(${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadInventory(${i})">${i}</a></li>`;
    }
    
    // Next button
    if (pagination.next_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadInventory(${pagination.current_page + 1})">Next</a></li>`;
    }
    
    html += '</ul></nav>';
    container.innerHTML = html;
}

function applyFilters() {
    currentFilters = {
        status: document.getElementById('statusFilter').value,
        blood_type: document.getElementById('bloodTypeFilter').value,
        expiration: document.getElementById('expirationFilter').value,
        search: document.getElementById('searchFilter').value
    };
    loadInventory(1);
}

function viewInventory(stockId) {
    // Set the current inventory ID for status updates
    currentInventoryId = stockId;
    
    // Show loading state
    document.getElementById('inventoryDetailsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading inventory details...</p>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('inventoryDetailsModal')).show();

    fetch(`/admin/inventory/${stockId}`, {
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
            const daysLeft = calculateDaysLeft(data.expiration_date);
            document.getElementById('inventoryDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Stock ID:</strong> ${data.STOCK_ID}</p>
                        <p><strong>Donor:</strong> ${data.donor && data.donor.name ? data.donor.name : (data.donor ? `User ID: ${data.donor}` : 'Anonymous Donor')}</p>
                        <p><strong>Blood Type:</strong> <span class="badge bg-danger">${data.blood_type}</span></p>
                        <p><strong>Quantity:</strong> ${data.quantity} units</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Acquisition Date:</strong> ${formatDate(data.acquisition_date)}</p>
                        <p><strong>Expiration Date:</strong> ${formatDate(data.expiration_date)}</p>
                        <p><strong>Status:</strong> <span class="badge bg-${data.status ? 'success' : 'secondary'}">${data.status ? 'Available' : 'Unavailable'}</span></p>
                        <p><strong>Days Left:</strong> <span class="badge bg-${getDaysLeftColor(daysLeft)}">${daysLeft > 0 ? `${daysLeft} days` : 'Expired'}</span></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Click "Update Status" below to change the inventory status.
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading inventory details:', error);
            document.getElementById('inventoryDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> Failed to load inventory details. Please try again.
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="viewInventory(${stockId})">
                        <i class="bi bi-arrow-clockwise me-1"></i>Retry
                    </button>
                </div>
            `;
            showNotification('Error loading inventory details. Please try again.', 'error');
        });
}

function openStatusUpdate(stockId) {
    currentInventoryId = stockId;
    document.getElementById('newStatus').value = '';
    document.getElementById('adminNotes').value = '';
    new bootstrap.Modal(document.getElementById('statusUpdateModal')).show();
}

function submitStatusUpdate() {
    const newStatus = document.getElementById('newStatus').value;
    const adminNotes = document.getElementById('adminNotes').value;

    if (!newStatus) {
        showNotification('Please select a new status', 'error');
        return;
    }

        fetch(`/admin/inventory/${currentInventoryId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                status: newStatus,
                admin_notes: adminNotes
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
            showNotification('Inventory status updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
            loadInventory(currentPage);
            loadInventoryStats();
        } else {
            showNotification(data.message || 'Failed to update inventory status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating inventory status:', error);
        showNotification(`Error updating inventory status: ${error.message}`, 'error');
    });
}

function removeAllExpired() {
    if (confirm('Are you sure you want to remove ALL expired blood units? This action cannot be undone.')) {
        fetch(`/admin/inventory/remove-expired`, {
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
                showNotification(`Successfully removed ${data.removed_count || 0} expired blood units`, 'success');
                loadInventory(currentPage);
                loadInventoryStats();
            } else {
                showNotification(data.message || 'Failed to remove expired blood units', 'error');
            }
        })
        .catch(error => {
            console.error('Error removing expired blood units:', error);
            showNotification(`Error removing expired blood units: ${error.message}`, 'error');
        });
    }
}

function removeExpired(stockId) {
    if (confirm('Are you sure you want to remove this blood unit?')) {
        fetch(`/admin/inventory/${stockId}`, {
            method: 'DELETE',
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
                showNotification('Blood unit removed successfully', 'success');
                loadInventory(currentPage);
                loadInventoryStats();
            } else {
                showNotification(data.message || 'Failed to remove blood unit', 'error');
            }
        })
        .catch(error => {
            console.error('Error removing blood unit:', error);
            showNotification(`Error removing blood unit: ${error.message}`, 'error');
        });
    }
}

function updateInventoryStatus() {
    // This function is called from the modal button
    // It will open the status update modal
    if (currentInventoryId) {
        openStatusUpdate(currentInventoryId);
    } else {
        showNotification('No inventory item selected for status update', 'error');
    }
}

function viewLowStock() {
    currentFilters = { ...currentFilters, low_stock: true };
    loadInventory(1);
}

function viewExpiring() {
    currentFilters = { ...currentFilters, expiring: true };
    loadInventory(1);
}

function viewExpired() {
    currentFilters = { ...currentFilters, expired: true };
    loadInventory(1);
}

function viewTotal() {
    currentFilters = {};
    loadInventory(1);
}

function exportInventory() {
    console.log('Export function called');
    console.log('Current filters:', currentFilters);
    const params = new URLSearchParams(currentFilters);
    console.log('URL params:', params.toString());
    const exportUrl = `{{ route('admin.inventory.export') }}?${params}`;
    console.log('Export URL:', exportUrl);
    window.open(exportUrl, '_blank');
}

function refreshInventory() {
    loadInventory(currentPage);
    loadInventoryStats();
}

function calculateDaysLeft(expirationDate) {
    if (!expirationDate) return 0;
    const today = new Date();
    const expiration = new Date(expirationDate);
    const diffTime = expiration - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

function getDaysLeftColor(daysLeft) {
    if (daysLeft <= 0) return 'danger';
    if (daysLeft <= 7) return 'warning';
    if (daysLeft <= 14) return 'info';
    return 'success';
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

// Load inventory on page load
document.addEventListener('DOMContentLoaded', function() {
    loadInventory();
    loadInventoryStats();
});
</script>
@endpush
