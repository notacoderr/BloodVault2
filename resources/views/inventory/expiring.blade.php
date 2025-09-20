@extends('layouts.app')

@section('title', 'Expiring Soon - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-clock text-warning me-2"></i>
                    Expiring Soon
                </h2>
                <div>
                    <a href="{{ route('admin.inventory.list') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i>View All Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        @php
            $critical = $inventory->where('expiration_date', '<=', now()->addDays(7))->count();
            $warning = $inventory->where('expiration_date', '>', now()->addDays(7))->where('expiration_date', '<=', now()->addDays(14))->count();
            $info = $inventory->where('expiration_date', '>', now()->addDays(14))->count();
        @endphp
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-danger mb-2">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-danger mb-1">{{ $critical }}</h4>
                    <p class="text-muted mb-0">Critical (≤7 days)</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-warning mb-1">{{ $warning }}</h4>
                    <p class="text-muted mb-0">Warning (8-14 days)</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-info mb-2">
                        <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-info mb-1">{{ $info }}</h4>
                    <p class="text-muted mb-0">Info (15-30 days)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Units Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        Blood Units Expiring in Next 30 Days
                    </h5>
                </div>
                <div class="card-body">
                    @if($inventory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Blood Type</th>
                                        <th>Quantity</th>
                                        <th>Donor</th>
                                        <th>Donation Date</th>
                                        <th>Expiration Date</th>
                                        <th>Days Left</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventory as $item)
                                    <tr class="@if(now()->diffInDays($item->expiration_date, false) <= 7) table-danger @elseif(now()->diffInDays($item->expiration_date, false) <= 14) table-warning @endif">
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->blood_type }}</span>
                                        </td>
                                        <td>{{ $item->quantity }} units</td>
                                        <td>{{ $item->donor_name ?? ($item->donor ? 'User ID: ' . $item->donor : 'Anonymous Donor') }}</td>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td>{{ $item->expiration_date->format('M d, Y') }}</td>
                                        <td>
                                            @php
                                                $daysLeft = now()->diffInDays($item->expiration_date, false);
                                            @endphp
                                            @if($daysLeft <= 7)
                                                <span class="badge bg-danger">{{ $daysLeft }} days</span>
                                            @elseif($daysLeft <= 14)
                                                <span class="badge bg-warning">{{ $daysLeft }} days</span>
                                            @else
                                                <span class="badge bg-info">{{ $daysLeft }} days</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        onclick="viewDetails({{ $item->id }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                @if($daysLeft > 0)
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="extendExpiration({{ $item->id }})">
                                                    <i class="bi bi-calendar-plus"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($inventory->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $inventory->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                            <h4 class="text-success mt-3 mb-2">No Expiring Units</h4>
                            <p class="text-muted mb-0">All blood units have sufficient time before expiration.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($inventory->count() > 0)
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Recommended Actions</h5>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button type="button" class="btn btn-warning" onclick="sendExpirationAlerts()">
                            <i class="bi bi-envelope me-2"></i>Send Alerts
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="exportInventory()">
                            <i class="bi bi-download me-2"></i>Export Report
                        </button>
                        <button type="button" class="btn btn-danger" onclick="removeExpired()">
                            <i class="bi bi-trash me-2"></i>Remove Expired
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Inventory Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Extend Expiration Modal -->
<div class="modal fade" id="extendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extend Expiration Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="extendForm">
                    <div class="mb-3">
                        <label for="newExpirationDate" class="form-label">New Expiration Date</label>
                        <input type="date" class="form-control" id="newExpirationDate" required>
                    </div>
                    <div class="mb-3">
                        <label for="extensionReason" class="form-label">Reason for Extension</label>
                        <textarea class="form-control" id="extensionReason" rows="3" placeholder="Enter reason for extending expiration date..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmExtend()">Extend</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentItemId = null;

function viewDetails(id) {
    // Load details via AJAX and show modal
    fetch(`/admin/inventory/${id}/details`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('detailsModalBody').innerHTML = html;
            new bootstrap.Modal(document.getElementById('detailsModal')).show();
        })
        .catch(error => {
            console.error('Error loading details:', error);
            alert('Error loading details');
        });
}

function extendExpiration(id) {
    currentItemId = id;
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('newExpirationDate').min = tomorrow.toISOString().split('T')[0];
    
    new bootstrap.Modal(document.getElementById('extendModal')).show();
}

function confirmExtend() {
    const newDate = document.getElementById('newExpirationDate').value;
    const reason = document.getElementById('extensionReason').value;
    
    if (!newDate) {
        alert('Please select a new expiration date');
        return;
    }
    
    fetch(`/admin/inventory/${currentItemId}/extend`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            expiration_date: newDate,
            reason: reason 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error extending expiration: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error extending expiration');
    });
}

function sendExpirationAlerts() {
    if (!confirm('Send expiration alerts to relevant staff members?')) {
        return;
    }
    
    fetch('/admin/inventory/send-expiration-alerts', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Expiration alerts sent successfully');
        } else {
            alert('Error sending alerts: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending alerts');
    });
}

function removeExpired() {
    if (!confirm('Remove all expired blood units? This action cannot be undone.')) {
        return;
    }
    
    fetch('/admin/inventory/remove-expired', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Expired units removed successfully');
            location.reload();
        } else {
            alert('Error removing expired units: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing expired units');
    });
}


</script>
@endpush
