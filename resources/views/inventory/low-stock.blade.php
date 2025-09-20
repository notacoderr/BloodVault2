@extends('layouts.app')

@section('title', 'Low Stock Alerts - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Low Stock Alerts
                </h2>
                <div>
                    <a href="{{ route('admin.inventory.list') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i>View All Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Summary -->
    @if($lowStockData && count($lowStockData) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Low Stock Warning</h5>
                        <p class="mb-0">The following blood types have critically low inventory levels and may need immediate attention.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Low Stock Cards -->
    <div class="row">
        @if($lowStockData && count($lowStockData) > 0)
            @foreach($lowStockData as $bloodType => $data)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-warning text-dark border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-droplet-fill text-danger me-2"></i>
                                {{ $bloodType }}
                            </h5>
                            <span class="badge bg-danger fs-6">{{ $data['available'] }} units</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-danger mb-1">{{ $data['available'] }}</h4>
                                    <small class="text-muted">Available</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-muted mb-1">{{ $data['available'] }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        
                        <div class="progress mb-3" style="height: 8px;">
                            @php
                                $percentage = ($data['available'] / 10) * 100; // Assuming 10 is normal level
                                $percentage = min(100, max(0, $percentage));
                            @endphp
                            <div class="progress-bar bg-danger" style="width: {{ $percentage }}%"></div>
                        </div>
                        
                        <div class="d-grid">
                            @if($data['available'] <= 2)
                                <span class="badge bg-danger mb-2">Critical Level</span>
                            @else
                                <span class="badge bg-warning mb-2">Low Level</span>
                            @endif
                        </div>
                        
                        <div class="mt-3">
                            <h6 class="mb-2">Available Units:</h6>
                            @if(isset($data['units']) && $data['units']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Quantity</th>
                                                <th>Expires</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['units']->take(3) as $unit)
                                            <tr>
                                                <td>{{ $unit->id }}</td>
                                                <td>{{ $unit->quantity }}</td>
                                                <td>
                                                    @php
                                                        $daysLeft = now()->diffInDays($unit->expiration_date, false);
                                                    @endphp
                                                    @if($daysLeft <= 7)
                                                        <span class="badge bg-danger">{{ $daysLeft }} days</span>
                                                    @else
                                                        <span class="text-muted">{{ $unit->expiration_date->format('M d') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($data['units']->count() > 3)
                                    <small class="text-muted">... and {{ $data['units']->count() - 3 }} more</small>
                                @endif
                            @else
                                <p class="text-muted text-center mb-0">No units available</p>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0">
                        <div class="d-grid">
                            <a href="{{ route('admin.inventory.by-type', $bloodType) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                        <h4 class="text-success mt-3 mb-2">All Good!</h4>
                        <p class="text-muted mb-0">All blood types have sufficient inventory levels.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>


</div>
@endsection


