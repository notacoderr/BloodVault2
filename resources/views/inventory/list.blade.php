@extends('layouts.app')

@section('title', 'Blood Inventory List - Life Vault')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-droplet-fill text-danger me-2"></i>
                    Blood Inventory List
                </h2>
                <div>
                    <button type="button" class="btn btn-outline-primary me-2" onclick="refreshInventory()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="bloodTypeFilter" class="form-label">Blood Type</label>
                            <select class="form-select" id="bloodTypeFilter">
                                <option value="">All Types</option>
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
                        
                        <div class="col-md-3 mb-3">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="1">Available</option>
                                <option value="0">Pending</option>
                                <option value="2">Expired</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="searchFilter" class="form-label">Search</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search inventory...">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                    <i class="bi bi-funnel me-2"></i>Apply Filters
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                    <i class="bi bi-x-circle me-2"></i>Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Blood Type</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Donor</th>
                                    <th>Donation Date</th>
                                    <th>Expiration Date</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                @if($inventory->count() > 0)
                                    @foreach($inventory as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->blood_type }}</span>
                                        </td>
                                        <td>{{ $item->quantity }} units</td>
                                        <td>
                                            @if($item->status == 1)
                                                <span class="badge bg-success">Available</span>
                                            @elseif($item->status == 0)
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->donor_name)
                                                {{ $item->donor_name }}
                                                @if(config('app.debug'))
                                                    <br><small class="text-muted">User ID: {{ $item->donor_user_id }}</small>
                                                @endif
                                            @elseif($item->donor)
                                                User ID: {{ $item->donor }}
                                            @else
                                                Anonymous Donor
                                            @endif
                                        </td>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @php
                                                $daysLeft = now()->diffInDays($item->expiration_date, false);
                                            @endphp
                                            <span class="text-muted">{{ $item->expiration_date->format('M d, Y') }}</span>
                                            @if($daysLeft <= 7 && $daysLeft > 0)
                                                <span class="badge bg-danger ms-1">{{ $daysLeft }} days</span>
                                            @elseif($daysLeft <= 14 && $daysLeft > 0)
                                                <span class="badge bg-warning ms-1">{{ $daysLeft }} days</span>
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">No inventory items found</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($inventory->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $inventory->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
function applyFilters() {
    const bloodType = document.getElementById('bloodTypeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchFilter').value;
    
    // Show loading state
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Applying...';
    button.disabled = true;
    
    // Build query string
    let query = '?';
    if (bloodType) query += `blood_type=${bloodType}&`;
    if (status !== '') query += `status=${status}&`;
    if (search) query += `search=${encodeURIComponent(search)}&`;
    
    // Remove trailing & if exists
    if (query.endsWith('&')) {
        query = query.slice(0, -1);
    }
    
    // Reload page with filters
    window.location.href = '{{ route("admin.inventory.list") }}' + query;
}







// Auto-apply filters on select change
document.getElementById('bloodTypeFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);

// Search on Enter key
document.getElementById('searchFilter').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

// Add notification function
function showNotification(message, type = 'info') {
    // Check if global notification functions exist
    if (typeof window.showSuccess === 'function' && typeof window.showError === 'function') {
        if (type === 'success') {
            window.showSuccess(message);
        } else if (type === 'danger' || type === 'error') {
            window.showError(message);
        } else {
            window.showInfo(message);
        }
    } else {
        // Fallback to simple alert
        alert(message);
    }
}

// Add loading spinner CSS
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// Refresh inventory function
function refreshInventory() {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Refreshing...';
    button.disabled = true;
    
    // Clear filters
    document.getElementById('bloodTypeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('searchFilter').value = '';
    
    // Reload page
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Clear filters function
function clearFilters() {
    document.getElementById('bloodTypeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('searchFilter').value = '';
    
    // Reload page without filters
    window.location.href = '{{ route("admin.inventory.list") }}';
}


</script>
@endpush
