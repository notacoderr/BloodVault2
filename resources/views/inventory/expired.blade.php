@extends('layouts.app')

@section('title', 'Expired Blood Units - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-x-circle text-danger me-2"></i>
                    Expired Blood Units
                </h2>

            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        @php
            $totalExpired = $expiredUnits->count();
            $totalQuantity = $expiredUnits->sum('quantity');
            $oldestExpired = $expiredUnits->max('expiration_date');
            $newestExpired = $expiredUnits->min('expiration_date');
        @endphp
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-danger mb-2">
                        <i class="bi bi-x-circle-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-danger mb-1">{{ $totalExpired }}</h4>
                    <p class="text-muted mb-0">Total Expired Items</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-danger mb-2">
                        <i class="bi bi-droplet-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-danger mb-1">{{ $totalQuantity }}</h4>
                    <p class="text-muted mb-0">Total Expired Units</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-warning mb-1">{{ $oldestExpired ? $oldestExpired->format('M d') : 'N/A' }}</h4>
                    <p class="text-muted mb-0">Oldest Expired</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-info mb-2">
                        <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-info mb-1">{{ $newestExpired ? $newestExpired->format('M d') : 'N/A' }}</h4>
                    <p class="text-muted mb-0">Most Recently Expired</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Expired Units Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-x me-2"></i>
                            Expired Blood Units
                        </h5>
                        @if($expiredUnits->count() > 0)
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeAllExpired()">
                            <i class="bi bi-trash me-2"></i>Remove All Expired
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($expiredUnits->count() > 0)
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
                                        <th>Days Expired</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiredUnits as $item)
                                    <tr class="table-danger">
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
                                                $daysExpired = now()->diffInDays($item->expiration_date);
                                            @endphp
                                            <span class="badge bg-danger">{{ $daysExpired }} days</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        onclick="viewDetails({{ $item->id }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="removeExpired({{ $item->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($expiredUnits->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $expiredUnits->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                            <h4 class="text-success mt-3 mb-2">No Expired Units</h4>
                            <p class="text-muted mb-0">All blood units are within their expiration dates.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    @if($expiredUnits->count() > 0)
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Recommended Actions</h5>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button type="button" class="btn btn-danger" onclick="removeAllExpired()">
                            <i class="bi bi-trash me-2"></i>Remove All Expired
                        </button>
                        <a href="{{ route('admin.inventory.export') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-download me-2"></i>Export Report
                        </a>
                        <button type="button" class="btn btn-warning" onclick="sendExpiredAlerts()">
                            <i class="bi bi-envelope me-2"></i>Send Staff Alerts
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
                <h5 class="modal-title">Expired Inventory Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

function removeExpired(id) {
    if (!confirm('Remove this expired blood unit? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/inventory/${id}/remove`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error removing expired unit: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing expired unit');
    });
}

function removeAllExpired() {
    if (!confirm('Remove ALL expired blood units? This action cannot be undone and will affect ' + {{ $expiredUnits->count() }} + ' items.')) {
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
            alert('All expired units removed successfully');
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

function sendExpiredAlerts() {
    if (!confirm('Send alerts to staff about expired blood units?')) {
        return;
    }
    
    fetch('/admin/inventory/send-expired-alerts', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Expired unit alerts sent successfully');
        } else {
            alert('Error sending alerts: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending alerts');
    });
}


</script>
@endpush
