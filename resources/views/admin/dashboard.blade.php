@extends('layouts.app')

@section('title', 'Admin Dashboard - Life Vault')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="bi bi-gear text-danger me-2"></i>
            Admin Dashboard
        </h2>
        <p class="text-muted mb-0">Manage the Life Vault blood bank system and monitor activities.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users') }}" class="btn btn-danger">
            <i class="bi bi-people me-2"></i>Manage Users
        </a>
        <a href="{{ route('admin.inventory') }}" class="btn btn-outline-danger">
            <i class="bi bi-boxes me-2"></i>Blood Inventory
        </a>
        <a href="{{ route('admin.appointments') }}" class="btn btn-outline-info">
            <i class="bi bi-calendar-event me-2"></i>Appointments
        </a>
    </div>
</div>

<!-- System Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                </div>
                <h3 class="mb-1" id="total-users">-</h3>
                <p class="text-muted mb-0">Total Users</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-droplet text-success" style="font-size: 1.5rem;"></i>
                </div>
                <h3 class="mb-1" id="total-blood-units">-</h3>
                <p class="text-muted mb-0">Blood Units</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-list-check text-warning" style="font-size: 1.5rem;"></i>
                </div>
                <h3 class="mb-1" id="pending-requests">-</h3>
                <p class="text-muted mb-0">Pending Requests</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-calendar-check text-info" style="font-size: 1.5rem;"></i>
                </div>
                <h3 class="mb-1" id="today-appointments">-</h3>
                <p class="text-muted mb-0">Today's Appointments</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.users') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-people text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Manage Users</h6>
                                    <p class="small text-muted mb-0">View and manage user accounts</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.blood-requests') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-list-check text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Blood Requests</h6>
                                    <p class="small text-muted mb-0">Review and approve blood requests</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.inventory') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-boxes text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Blood Inventory</h6>
                                    <p class="small text-muted mb-0">Manage blood stock and inventory</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.blood-donations') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-droplet text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Blood Donations</h6>
                                    <p class="small text-muted mb-0">Manage blood donations and screening</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.appointments') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-calendar-event text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Appointments</h6>
                                    <p class="small text-muted mb-0">Manage user appointments and scheduling</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.email.bulk') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-envelope text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Send Emails</h6>
                                    <p class="small text-muted mb-0">Send bulk emails to users</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>
                    Recent Blood Requests
                </h5>
                <a href="{{ route('admin.blood-requests') }}" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="card-body">
                <div id="recent-requests">
                    @if($recentRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Requester</th>
                                        <th>Blood Type</th>
                                        <th>Units</th>
                                        <th>Urgency</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRequests as $request)
                                    <tr>
                                        <td>{{ $request->user->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-danger">{{ $request->blood_type }}</span></td>
                                        <td>{{ $request->units_needed }}</td>
                                        <td>
                                            <span class="badge bg-{{ $request->urgency === 'critical' ? 'danger' : ($request->urgency === 'high' ? 'warning' : ($request->urgency === 'medium' ? 'info' : 'success')) }}">
                                                {{ $request->urgency }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'info' : ($request->status === 'completed' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                                {{ $request->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">No recent requests</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-heart me-2"></i>
                    Recent Blood Donations
                </h5>
                <a href="{{ route('admin.blood-donations') }}" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="card-body">
                <div id="recent-donations">
                    @if($recentDonations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Donor</th>
                                        <th>Blood Type</th>
                                        <th>Screening</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentDonations as $donation)
                                    <tr>
                                        <td>{{ $donation->user->name ?? $donation->donor_name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-danger">{{ $donation->blood_type }}</span></td>
                                        <td>
                                            @if($donation->screening_answers)
                                                <span class="badge bg-info">Completed</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $donation->status === 'pending' ? 'warning' : ($donation->status === 'approved' ? 'info' : ($donation->status === 'completed' ? 'success' : 'danger')) }}">
                                                {{ $donation->status }}
                                            </span>
                                        </td>
                                        <td>{{ $donation->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2">No recent donations</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Alerts -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Low Stock Alerts
                </h5>
                <a href="{{ route('admin.inventory.low-stock') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body">
                <div id="low-stock-alerts">
                    <div class="text-center py-4">
                        <i class="bi bi-arrow-clockwise text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-clock me-2"></i>
                    Expiring Soon
                </h5>
                <a href="{{ route('admin.inventory.expiring') }}" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="card-body">
                <div id="expiring-soon">
                    <div class="text-center py-4">
                        <i class="bi bi-arrow-clockwise text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Blood Type Distribution Chart -->
<div class="row">
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
                    <div class="col-md-8">
                        <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                            <canvas id="bloodTypeChart"></canvas>
                            <div id="chart-placeholder" class="text-center py-4" style="display: none;">
                                <i class="bi bi-pie-chart text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">No blood type data available</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="blood-type-legend">
                            <div class="text-center py-4">
                                <i class="bi bi-arrow-clockwise text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin dashboard DOM loaded');
    
    // Check if Chart.js is available
    if (typeof Chart === 'undefined') {
        console.error('Chart.js library not loaded. Please check the CDN link.');
        document.getElementById('blood-type-legend').innerHTML = '<p class="text-danger">Chart.js library not loaded. Please refresh the page.</p>';
    } else {
        console.log('Chart.js library loaded successfully');
    }
    
    // Load admin dashboard statistics
    loadAdminStats();
    
    // Load recent activities
    loadRecentActivities();
    
    // Load system alerts
    loadSystemAlerts();
    
    // Load blood type distribution
    loadBloodTypeDistribution();
    
    // Refresh data every 30 seconds
    setInterval(function() {
        loadAdminStats();
        loadRecentActivities();
        loadSystemAlerts();
        loadBloodTypeDistribution();
    }, 30000);
    

});

function loadAdminStats() {
    fetch('{{ route("admin.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-users').textContent = data.total_users || 0;
            document.getElementById('total-blood-units').textContent = data.total_blood_units || 0;
            document.getElementById('pending-requests').textContent = data.pending_requests || 0;
            document.getElementById('today-appointments').textContent = data.today_appointments || 0;
        })
        .catch(error => {
            console.error('Error loading admin stats:', error);
        });
}

function loadRecentActivities() {
    // Load recent blood requests
    fetch('{{ route("admin.blood-requests") }}?limit=5', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.requests && data.requests.length > 0) {
                const html = generateRequestsTable(data.requests);
                document.getElementById('recent-requests').innerHTML = html;
            } else {
                document.getElementById('recent-requests').innerHTML = 
                    '<div class="text-center py-4"><p class="text-muted">No recent requests</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading recent requests:', error);
            document.getElementById('recent-requests').innerHTML = 
                '<div class="text-center py-4"><p class="text-muted">Error loading requests</p></div>';
        });
    
    // Load recent blood donations
    fetch('{{ route("admin.blood-donations") }}?limit=5', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.donations && data.donations.length > 0) {
                const html = generateDonationsTable(data.donations);
                document.getElementById('recent-donations').innerHTML = html;
            } else {
                document.getElementById('recent-donations').innerHTML = 
                    '<div class="text-center py-4"><p class="text-muted">No recent donations</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading recent donations:', error);
            document.getElementById('recent-donations').innerHTML = 
                '<div class="text-center py-4"><p class="text-muted">Error loading donations</p></div>';
        });
}

function loadSystemAlerts() {
    // Load low stock alerts - use the stats endpoint instead of the full page
    fetch('{{ route("admin.inventory.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.low_stock_data && Object.keys(data.low_stock_data).length > 0) {
                const html = generateLowStockTable(data.low_stock_data);
                document.getElementById('low-stock-alerts').innerHTML = html;
            } else {
                document.getElementById('low-stock-alerts').innerHTML = 
                    '<div class="text-center py-4"><p class="text-muted">No low stock alerts</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading low stock alerts:', error);
            document.getElementById('low-stock-alerts').innerHTML = 
                '<div class="text-center py-4"><p class="text-muted">Error loading alerts</p></div>';
        });
    
    // Load expiring soon - use the stats endpoint instead of the full page
    fetch('{{ route("admin.inventory.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.expiring_soon_data && data.expiring_soon_data.length > 0) {
                const html = generateExpiringTable(data.expiring_soon_data.slice(0, 5)); // Limit to 5 items
                document.getElementById('expiring-soon').innerHTML = html;
            } else {
                document.getElementById('expiring-soon').innerHTML = 
                    '<div class="text-center py-4"><p class="text-muted">No expiring blood units</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading expiring soon:', error);
            document.getElementById('expiring-soon').innerHTML = 
                '<div class="text-center py-4"><p class="text-muted">Error loading expiring data</p></div>';
        });
}

function loadBloodTypeDistribution() {
    console.log('Loading blood type distribution...');
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        document.getElementById('blood-type-legend').innerHTML = '<p class="text-danger">Chart.js library not loaded</p>';
        return;
    }
    
    // Check if canvas element exists
    const canvas = document.getElementById('bloodTypeChart');
    if (!canvas) {
        console.error('Blood type chart canvas not found');
        document.getElementById('blood-type-legend').innerHTML = '<p class="text-danger">Chart canvas not found</p>';
        return;
    }
    
    // Add timeout to prevent infinite loading
    const timeoutId = setTimeout(() => {
        console.log('Blood type distribution loading timeout');
        document.getElementById('blood-type-legend').innerHTML = '<p class="text-warning">Loading timeout - please refresh</p>';
    }, 10000); // 10 second timeout
    
    fetch('{{ route("admin.inventory.stats") }}')
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Blood type data received:', data);
            if (data.blood_type_breakdown && Object.keys(data.blood_type_breakdown).length > 0) {
                // Clear any existing chart
                if (bloodTypeChart) {
                    bloodTypeChart.destroy();
                    bloodTypeChart = null;
                }
                createBloodTypeChart(data.blood_type_breakdown);
                createBloodTypeLegend(data.blood_type_breakdown);
            } else {
                console.log('No blood type breakdown data available');
                // Show empty state
                document.getElementById('blood-type-legend').innerHTML = '<p class="text-muted">No blood type data available</p>';
                // Clear chart area
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            console.error('Error loading blood type distribution:', error);
            // Show error state
            document.getElementById('blood-type-legend').innerHTML = '<p class="text-danger">Error loading data: ' + error.message + '</p>';
            // Clear chart area
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });
}

function generateRequestsTable(requests) {
    let html = '<div class="table-responsive"><table class="table table-sm">';
    html += '<thead><tr><th>Requester</th><th>Blood Type</th><th>Units</th><th>Urgency</th><th>Status</th></tr></thead><tbody>';
    
    requests.forEach(request => {
        html += `<tr>
            <td>${request.user ? request.user.name : 'N/A'}</td>
            <td><span class="badge bg-danger">${request.blood_type}</span></td>
            <td>${request.units_needed}</td>
            <td><span class="badge bg-${getUrgencyColor(request.urgency)}">${request.urgency}</span></td>
            <td><span class="badge bg-${getStatusColor(request.status)}">${request.status}</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    return html;
}

function generateDonationsTable(donations) {
    let html = '<div class="table-responsive"><table class="table table-sm">';
    html += '<thead><tr><th>Donor</th><th>Blood Type</th><th>Screening</th><th>Status</th><th>Date</th></tr></thead><tbody>';
    
    donations.forEach(donation => {
        const screeningStatus = donation.screening_answers ? 
            '<span class="badge bg-info">Completed</span>' : 
            '<span class="badge bg-secondary">Pending</span>';
        
        html += `<tr>
            <td>${donation.user ? donation.user.name : (donation.donor_name || 'N/A')}</td>
            <td><span class="badge bg-danger">${donation.blood_type}</span></td>
            <td>${screeningStatus}</td>
            <td><span class="badge bg-${getStatusColor(donation.status)}">${donation.status}</span></td>
            <td>${formatDate(donation.created_at)}</td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    return html;
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

let bloodTypeChart = null; // Global variable to track chart instance

function createBloodTypeChart(bloodTypeData) {
    try {
        const canvas = document.getElementById('bloodTypeChart');
        if (!canvas) {
            console.error('Blood type chart canvas not found');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Could not get 2D context from canvas');
            return;
        }
        
        // Destroy existing chart if it exists
        if (bloodTypeChart) {
            bloodTypeChart.destroy();
        }
        
        const labels = Object.keys(bloodTypeData);
        const data = labels.map(type => bloodTypeData[type].available);
        
        // Check if we have valid data
        if (data.length === 0 || data.every(val => val === 0)) {
            console.log('No blood type data available for chart');
            // Show placeholder
            const placeholder = document.getElementById('chart-placeholder');
            if (placeholder) {
                placeholder.style.display = 'block';
            }
            return;
        }
        
        // Hide placeholder if we have data
        const placeholder = document.getElementById('chart-placeholder');
        if (placeholder) {
            placeholder.style.display = 'none';
        }
        
        const colors = [
            '#dc3545', '#fd7e14', '#ffc107', '#198754',
            '#0dcaf0', '#6f42c1', '#d63384', '#6c757d'
        ];
        
        console.log('Creating blood type chart with data:', { labels, data });
        
        bloodTypeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                return `${label}: ${value} units`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                },
                layout: {
                    padding: 20
                }
            }
        });
        
        console.log('Blood type chart created successfully');
    } catch (error) {
        console.error('Error creating blood type chart:', error);
        document.getElementById('blood-type-legend').innerHTML = '<p class="text-danger">Error creating chart: ' + error.message + '</p>';
    }
}

function createBloodTypeLegend(bloodTypeData) {
    try {
        const legendContainer = document.getElementById('blood-type-legend');
        if (!legendContainer) {
            console.error('Blood type legend container not found');
            return;
        }
        
        if (!bloodTypeData || Object.keys(bloodTypeData).length === 0) {
            legendContainer.innerHTML = '<p class="text-muted">No blood type data available</p>';
            return;
        }
        
        let legendHTML = '<h6 class="mb-3">Available Units</h6>';
        
        const colors = [
            '#dc3545', '#fd7e14', '#ffc107', '#198754',
            '#0dcaf0', '#6f42c1', '#d63384', '#6c757d'
        ];
        
        let hasData = false;
        Object.entries(bloodTypeData).forEach(([type, data], index) => {
            if (data.available > 0) {
                hasData = true;
                legendHTML += `
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2" style="width: 20px; height: 20px; background-color: ${colors[index % colors.length]}; border-radius: 50%;"></div>
                        <span class="fw-bold">${type}:</span>
                        <span class="ms-auto">${data.available} units</span>
                    </div>
                `;
            }
        });
        
        if (!hasData) {
            legendHTML = '<p class="text-muted">No blood units currently available</p>';
        }
        
        legendContainer.innerHTML = legendHTML;
        console.log('Blood type legend created successfully');
    } catch (error) {
        console.error('Error creating blood type legend:', error);
        const legendContainer = document.getElementById('blood-type-legend');
        if (legendContainer) {
            legendContainer.innerHTML = '<p class="text-danger">Error creating legend: ' + error.message + '</p>';
        }
    }
}

function generateLowStockTable(lowStockData) {
    let html = '<div class="table-responsive"><table class="table table-sm">';
    html += '<thead><tr><th>Blood Type</th><th>Available Units</th><th>Status</th></tr></thead><tbody>';
    
    Object.entries(lowStockData).forEach(([bloodType, data]) => {
        const status = data.available <= 2 ? 'Critical' : 'Low';
        const statusClass = data.available <= 2 ? 'danger' : 'warning';
        
        html += `<tr>
            <td><span class="badge bg-danger">${bloodType}</span></td>
            <td>${data.available} units</td>
            <td><span class="badge bg-${statusClass}">${status}</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    return html;
}

function generateExpiringTable(expiringData) {
    let html = '<div class="table-responsive"><table class="table table-sm">';
    html += '<thead><tr><th>Blood Type</th><th>Quantity</th><th>Expiration Date</th><th>Days Left</th></tr></thead><tbody>';
    
    expiringData.forEach(item => {
        const daysLeft = Math.ceil((new Date(item.expiration_date) - new Date()) / (1000 * 60 * 60 * 24));
        let daysLeftClass = 'info';
        if (daysLeft <= 7) daysLeftClass = 'danger';
        else if (daysLeft <= 14) daysLeftClass = 'warning';
        
        html += `<tr>
            <td><span class="badge bg-primary">${item.blood_type}</span></td>
            <td>${item.quantity} units</td>
            <td>${formatDate(item.expiration_date)}</td>
            <td><span class="badge bg-${daysLeftClass}">${daysLeft > 0 ? daysLeft + ' days' : 'Expired'}</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    return html;
}
</script>
@endpush

@push('styles')
<style>
.quick-action-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.quick-action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.bg-primary {
    background-color: #0d6efd !important;
}

.text-primary {
    color: #0d6efd !important;
}

.bg-success {
    background-color: #198754 !important;
}

.text-success {
    color: #198754 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.bg-info {
    background-color: #0dcaf0 !important;
}

.text-info {
    color: #0dcaf0 !important;
}

.btn-outline-warning {
    color: #ffc107;
    border-color: #ffc107;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-outline-success {
    color: #198754;
    border-color: #198754;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.chart-container {
    min-height: 300px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background-color: #f8f9fa;
}

#bloodTypeChart {
    max-height: 300px;
}

#chart-placeholder {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
}
</style>
@endpush
