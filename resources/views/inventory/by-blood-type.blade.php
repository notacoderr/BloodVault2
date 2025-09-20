@extends('layouts.app')

@section('title', $bloodType . ' Blood Type Inventory - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-droplet-fill text-danger me-2"></i>
                    {{ $bloodType }} Blood Type Inventory
                </h2>
                <div>
                    <a href="{{ route('admin.inventory.list') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i>View All Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Blood Type Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <i class="bi bi-droplet-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-primary mb-1">{{ $bloodTypeStats['total'] }}</h4>
                    <p class="text-muted mb-0">Total Units</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-success mb-1">{{ $bloodTypeStats['available'] }}</h4>
                    <p class="text-muted mb-0">Available</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="bi bi-clock-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-warning mb-1">{{ $bloodTypeStats['pending'] }}</h4>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-danger mb-2">
                        <i class="bi bi-x-circle-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-danger mb-1">{{ $bloodTypeStats['expired'] }}</h4>
                    <p class="text-muted mb-0">Expired</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        {{ $bloodType }} Status Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="statusChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle me-3" style="width: 20px; height: 20px;"></div>
                                    <span>Available ({{ $bloodTypeStats['available'] }})</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle me-3" style="width: 20px; height: 20px;"></div>
                                    <span>Pending ({{ $bloodTypeStats['pending'] }})</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger rounded-circle me-3" style="width: 20px; height: 20px;"></div>
                                    <span>Expired ({{ $bloodTypeStats['expired'] }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Items Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-list me-2"></i>
                        {{ $bloodType }} Inventory Items
                    </h5>
                </div>
                <div class="card-body">
                    @if($inventory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Donor</th>
                                        <th>Donation Date</th>
                                        <th>Expiration Date</th>
                                        <th>Days Left</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventory as $item)
                                    <tr class="@if($item->status == 2) table-danger @elseif($item->status == 0) table-warning @endif">
                                        <td>{{ $item->id }}</td>
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
                                        <td>{{ $item->donor_name ?? ($item->donor ? 'User ID: ' . $item->donor : 'Anonymous Donor') }}</td>
                                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                                        <td>{{ $item->expiration_date->format('M d, Y') }}</td>
                                        <td>
                                            @php
                                                $daysLeft = now()->diffInDays($item->expiration_date, false);
                                            @endphp
                                            @if($daysLeft <= 0)
                                                <span class="badge bg-danger">Expired</span>
                                            @elseif($daysLeft <= 7)
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
                                                @if($item->status == 1)
                                                <button type="button" class="btn btn-outline-warning" 
                                                        onclick="updateStatus({{ $item->id }}, 0)">
                                                    <i class="bi bi-pause"></i>
                                                </button>
                                                @elseif($item->status == 0)
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="updateStatus({{ $item->id }}, 1)">
                                                    <i class="bi bi-play"></i>
                                                </button>
                                                @endif
                                                @if($item->status == 2)
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="removeExpired({{ $item->id }})">
                                                    <i class="bi bi-trash"></i>
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
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3 mb-2">No {{ $bloodType }} Inventory</h4>
                            <p class="text-muted mb-0">No blood units found for this blood type.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('admin.blood-donations') }}" class="btn btn-success">
                            <i class="bi bi-heart-fill me-2"></i>View Donations
                        </a>
                        <a href="{{ route('admin.inventory.export') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-download me-2"></i>Export Report
                        </a>
                        <a href="{{ route('admin.email.bulk') }}" class="btn btn-primary">
                            <i class="bi bi-envelope me-2"></i>
                            Send Bulk Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create status chart
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Pending', 'Expired'],
            datasets: [{
                data: [
                    {{ $bloodTypeStats['available'] }},
                    {{ $bloodTypeStats['pending'] }},
                    {{ $bloodTypeStats['expired'] }}
                ],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});

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

function updateStatus(id, status) {
    if (!confirm('Are you sure you want to update the status of this inventory item?')) {
        return;
    }
    
    fetch(`/admin/inventory/${id}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
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


</script>
@endpush
