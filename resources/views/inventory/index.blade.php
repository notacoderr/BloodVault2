@extends('layouts.app')

@section('title', 'Blood Inventory - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-droplet-fill text-danger me-2"></i>
                    Blood Inventory Overview
                </h2>

            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <i class="bi bi-droplet-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="text-primary mb-1">{{ number_format($totalUnits) }}</h4>
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
                    <h4 class="text-success mb-1">{{ number_format($availableUnits) }}</h4>
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
                    <h4 class="text-warning mb-1">{{ number_format($pendingUnits) }}</h4>
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
                    <h4 class="text-danger mb-1">{{ number_format($expiredUnits) }}</h4>
                    <p class="text-muted mb-0">Expired</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Blood Type Distribution -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Blood Type Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($inventoryByType as $bloodType => $data)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="me-3">
                                    <div class="text-danger">
                                        <i class="bi bi-droplet-fill" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $bloodType }}</h6>
                                    <p class="mb-0 text-muted">
                                        {{ $data['available'] }} available / {{ $data['total'] }} total
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Donations -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-heart-fill text-danger me-2"></i>
                        Recent Donations
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentDonations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Donor</th>
                                        <th>Blood Type</th>
                                        <th>Quantity</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentDonations->take(5) as $donation)
                                    <tr>
                                        <td>{{ $donation->donor->name ?? 'Unknown' }}</td>
                                        <td><span class="badge bg-primary">{{ $donation->blood_type }}</span></td>
                                        <td>{{ $donation->quantity }} units</td>
                                        <td>{{ $donation->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No recent donations</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Low Stock Alerts
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($lowStockAlerts) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Blood Type</th>
                                        <th>Available</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockAlerts as $bloodType => $available)
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $bloodType }}</span></td>
                                        <td>{{ $available }} units</td>
                                        <td>
                                            @if($available <= 2)
                                                <span class="badge bg-danger">Critical</span>
                                            @else
                                                <span class="badge bg-warning">Low</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No low stock alerts</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Soon -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0">
                        <i class="bi bi-clock text-warning me-2"></i>
                        Expiring Soon (Next 30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    @if($expiringSoon->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Blood Type</th>
                                        <th>Quantity</th>
                                        <th>Expiration Date</th>
                                        <th>Days Left</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringSoon->take(10) as $item)
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $item->blood_type }}</span></td>
                                        <td>{{ $item->quantity }} units</td>
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
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No blood units expiring soon</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


