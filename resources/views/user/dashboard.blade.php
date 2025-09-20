@extends('layouts.app')

@section('title', 'Dashboard - Life Vault')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="bi bi-speedometer2 text-danger me-2"></i>
            Welcome back, {{ Auth::user()->name }}!
        </h2>
        <p class="text-muted mb-0">Here's what's happening with your account today.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('blood-request.create') }}" class="btn btn-danger">
            <i class="bi bi-plus-circle me-2"></i>Request Blood
        </a>
        <a href="{{ route('blood-donation.create') }}" class="btn btn-outline-danger" id="header-donate-blood-btn">
            <i class="bi bi-heart me-2"></i>Donate Blood
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-list-check text-danger" style="font-size: 1.5rem;"></i>
                </div>
                <h3 class="mb-1" id="total-requests">-</h3>
                <p class="text-muted mb-0">Blood Requests</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-heart text-danger" style="font-size: 1.5rem;"></i>
                </div>
                <h3 class="mb-1" id="total-donations">-</h3>
                <p class="text-muted mb-0">Blood Donations</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-calendar-check text-danger" style="font-size: 1.5rem;"></i>
                </div>
                <h3 class="mb-1" id="total-appointments">-</h3>
                <p class="text-muted mb-0">Appointments</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-droplet text-danger" style="font-size: 1.5rem;"></i>
                </div>
                <h3 class="mb-1">{{ Auth::user()->bloodtype }}</h3>
                <p class="text-muted mb-0">Blood Type</p>
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
                    <i class="bi bi-clock-history me-2"></i>
                    Status Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <h6 class="text-muted mb-2">Pending Blood Requests</h6>
                            <div id="pending-requests" class="d-flex justify-content-center">
                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <h6 class="text-muted mb-2">Pending Blood Donations</h6>
                            <div id="pending-donations" class="d-flex justify-content-center">
                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center">
                            <h6 class="text-muted mb-2">Pending Appointments</h6>
                            <div id="pending-appointments" class="d-flex justify-content-center">
                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Donation Cooldown Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="bi bi-heart-pulse me-2"></i>
                    Blood Donation Status
                </h5>
            </div>
            <div class="card-body">
                <div id="donation-cooldown-info">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-muted" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Loading donation status...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Notifications -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-bell me-2"></i>
                    Recent Notifications
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="loadNotifications()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="notifications-container">
                    <div class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-muted" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Loading notifications...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Helpful Tips & Guide -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Helpful Tips & Guide
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="guide-tip">
                            <div class="tip-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-droplet text-primary"></i>
                            </div>
                            <h6 class="mb-2">Blood Requests</h6>
                            <p class="small text-muted mb-0">Fill in all required fields marked with *. Provide detailed reason and hospital information for faster processing.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="guide-tip">
                            <div class="tip-icon bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-heart text-danger"></i>
                            </div>
                            <h6 class="mb-2">Blood Donations</h6>
                            <p class="small text-muted mb-0">Ensure you're well-rested and hydrated before donating. Bring a valid ID and complete the health screening.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="guide-tip">
                            <div class="tip-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-calendar-check text-success"></i>
                            </div>
                            <h6 class="mb-2">Appointments</h6>
                            <p class="small text-muted mb-0">Book appointments at least 24 hours in advance. You can reschedule or cancel up to 2 hours before.</p>
                        </div>
                    </div>
                </div>
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
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('blood-request.create') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-plus-circle text-danger mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Request Blood</h6>
                                    <p class="small text-muted mb-0">Request blood units when you need them</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('blood-donation.create') }}" class="text-decoration-none" id="donate-blood-link" title="Schedule your blood donation">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card" id="donate-blood-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-heart text-danger mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Donate Blood</h6>
                                    <p class="small text-muted mb-0">Schedule your blood donation</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('appointment.create') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-calendar-plus text-danger mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Book Appointment</h6>
                                    <p class="small text-muted mb-0">Schedule consultations or screenings</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('user.profile') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm h-100 text-center quick-action-card">
                                <div class="card-body p-3">
                                    <i class="bi bi-person text-danger mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="mb-1">Update Profile</h6>
                                    <p class="small text-muted mb-0">Manage your personal information</p>
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
                <a href="{{ route('user.my-requests') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body">
                <div id="recent-requests">
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
                    <i class="bi bi-heart me-2"></i>
                    Recent Blood Donations
                </h5>
                <a href="{{ route('user.my-donations') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body">
                <div id="recent-donations">
                    <div class="text-center py-4">
                        <i class="bi bi-arrow-clockwise text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Appointments -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-event me-2"></i>
                    Upcoming Appointments
                </h5>
                <a href="{{ route('user.my-appointments') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body">
                <div id="upcoming-appointments">
                    <div class="text-center py-4">
                        <i class="bi bi-arrow-clockwise text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load dashboard statistics
    loadDashboardStats();
    
    // Load pending status
    loadPendingStatus();
    
    // Load donation cooldown information
    loadDonationCooldown();
    
    // Load recent activities
    loadRecentActivities();
    
    // Load notifications
    loadNotifications();
    
    // Refresh data every 30 seconds
    setInterval(function() {
        loadDashboardStats();
        loadPendingStatus();
        loadDonationCooldown();
        loadRecentActivities();
        loadNotifications(); // Refresh notifications periodically
    }, 30000);
});

function loadNotifications() {
    const notificationsContainer = document.getElementById('notifications-container');
    notificationsContainer.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-muted" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2 mb-0">Loading notifications...</p>
        </div>
    `;

    // For now, show a simple "all caught up" message
    // In the future, this could load actual notifications from a proper endpoint
    setTimeout(() => {
        notificationsContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                </div>
                <h6 class="text-success mb-2">All Caught Up!</h6>
                <p class="text-muted mb-0">No new notifications at the moment. You're all set!</p>
            </div>
        `;
    }, 1000);
}

function loadPendingStatus() {
    // Load pending status counts from the new API endpoint
    fetch('{{ route("user.dashboard.status-counts") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Update pending blood requests
            if (data.pending_requests > 0) {
                document.getElementById('pending-requests').innerHTML = `
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="badge bg-warning fs-5 me-2">${data.pending_requests}</span>
                        <span class="text-muted">Pending</span>
                    </div>
                `;
            } else if (data.total_requests > 0) {
                document.getElementById('pending-requests').innerHTML = 
                    '<span class="badge bg-success fs-6">All Approved</span>';
            } else {
                document.getElementById('pending-requests').innerHTML = `
                    <div class="text-center">
                        <span class="badge bg-info fs-6">No Requests Yet</span>
                        <small class="d-block text-muted mt-1">Start by requesting blood</small>
                    </div>
                `;
            }
            
            // Update pending blood donations
            if (data.pending_donations > 0) {
                document.getElementById('pending-donations').innerHTML = `
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="badge bg-warning fs-5 me-2">${data.pending_donations}</span>
                        <span class="text-muted">Pending</span>
                    </div>
                `;
            } else if (data.total_donations > 0) {
                document.getElementById('pending-donations').innerHTML = 
                    '<span class="badge bg-success fs-6">All Approved</span>';
            } else {
                document.getElementById('pending-donations').innerHTML = `
                    <div class="text-center">
                        <span class="badge bg-info fs-6">No Donations Yet</span>
                        <small class="d-block text-muted mt-1">Ready to donate blood</small>
                    </div>
                `;
            }
            
            // Update pending appointments
            if (data.pending_appointments > 0) {
                document.getElementById('pending-appointments').innerHTML = `
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="badge bg-warning fs-5 me-2">${data.pending_appointments}</span>
                        <span class="text-muted">Pending</span>
                    </div>
                `;
            } else if (data.total_appointments > 0) {
                document.getElementById('pending-appointments').innerHTML = 
                    '<span class="badge bg-success fs-6">All Confirmed</span>';
            } else {
                document.getElementById('pending-appointments').innerHTML = `
                    <div class="text-center">
                        <span class="badge bg-info fs-6">No Appointments Yet</span>
                        <small class="d-block text-muted mt-1">Book your first appointment</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading pending status:', error);
            // Set default values on error with better messaging
            document.getElementById('pending-requests').innerHTML = `
                <div class="text-center">
                    <span class="badge bg-secondary fs-6">Unable to Load</span>
                    <small class="d-block text-muted mt-1">Please refresh the page</small>
                </div>
            `;
            document.getElementById('pending-donations').innerHTML = `
                <div class="text-center">
                    <span class="badge bg-secondary fs-6">Unable to Load</span>
                    <small class="d-block text-muted mt-1">Please refresh the page</small>
                </div>
            `;
            document.getElementById('pending-appointments').innerHTML = `
                <div class="text-center">
                    <span class="badge bg-secondary fs-6">Unable to Load</span>
                    <small class="d-block text-muted mt-1">Please refresh the page</small>
                </div>
            `;
        });
}

function loadDonationCooldown() {
    // Load donation cooldown information
    fetch('{{ route("user.dashboard.donation-cooldown") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('donation-cooldown-info');
            const donateLink = document.getElementById('donate-blood-link');
            const donateCard = document.getElementById('donate-blood-card');
            const headerDonateBtn = document.getElementById('header-donate-blood-btn');
            
            if (data.can_donate) {
                container.innerHTML = `
                    <div class="text-center">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-heart text-success" style="font-size: 1.5rem;"></i>
                        </div>
                        <h6 class="text-success mb-2">Ready to Donate!</h6>
                        <p class="text-muted mb-0">You are eligible to donate blood. Thank you for your generosity!</p>
                    </div>
                `;
                // Enable the donate blood link and card
                donateLink.href = '{{ route("blood-donation.create") }}';
                donateLink.title = 'Schedule your blood donation';
                donateCard.classList.remove('disabled');
                donateCard.style.opacity = '1';
                headerDonateBtn.href = '{{ route("blood-donation.create") }}';
                headerDonateBtn.title = 'Schedule your blood donation';
            } else {
                container.innerHTML = `
                    <div class="cooldown-container text-center">
                        <div class="cooldown-animation mb-3">
                            <div class="heart-beat-container">
                                <i class="bi bi-heart-pulse heart-beat"></i>
                            </div>
                            <div class="cooldown-clock">
                                <i class="bi bi-clock clock-spin"></i>
                            </div>
                        </div>
                        
                        <div class="cooldown-content">
                            <h4 class="text-warning mb-2 fw-bold">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Donation Cooldown Active
                            </h4>
                            
                            <div class="cooldown-timer mb-3">
                                <div class="countdown-card">
                                    <div class="countdown-number">${data.remaining_days}</div>
                                    <div class="countdown-label">Days Remaining</div>
                                </div>
                            </div>
                            
                            <div class="cooldown-info mb-3">
                                <p class="text-muted mb-2">
                                    <i class="bi bi-info-circle me-2"></i>
                                    For your safety and the quality of donated blood, you must wait before your next donation.
                                </p>
                                <div class="next-eligible-date">
                                    <strong>Next Eligible Date:</strong>
                                    <div class="date-display">${data.next_eligible_date}</div>
                                </div>
                            </div>
                            
                            <div class="cooldown-progress">
                                <div class="progress-info mb-2">
                                    <small class="text-muted">Cooldown Progress</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: ${Math.max(0, 100 - (data.remaining_days / 56 * 100))}%" 
                                         aria-valuenow="${Math.max(0, 100 - (data.remaining_days / 56 * 100))}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    ${Math.max(0, 56 - data.remaining_days)} of 56 days completed
                                </small>
                            </div>
                        </div>
                    </div>
                `;
                // Disable the donate blood link and card
                donateLink.removeAttribute('href');
                donateLink.title = `Donation cooldown active. You must wait ${data.remaining_days} more days. Next eligible date: ${data.next_eligible_date}`;
                donateCard.classList.add('disabled');
                donateCard.style.opacity = '0.6';
                headerDonateBtn.removeAttribute('href');
                headerDonateBtn.title = `Donation cooldown active. You must wait ${data.remaining_days} more days. Next eligible date: ${data.next_eligible_date}`;
            }
        })
        .catch(error => {
            console.error('Error loading donation cooldown:', error);
            const container = document.getElementById('donation-cooldown-info');
            const donateLink = document.getElementById('donate-blood-link');
            const donateCard = document.getElementById('donate-blood-card');
            const headerDonateBtn = document.getElementById('header-donate-blood-btn');
            
            container.innerHTML = `
                <div class="text-center">
                    <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-question-circle text-secondary" style="font-size: 1.5rem;"></i>
                    </div>
                    <h6 class="text-secondary mb-2">Status Unavailable</h6>
                    <p class="text-muted mb-0">Unable to load donation status. Please try again later.</p>
                </div>
            `;
            // Ensure links are enabled if status is unavailable
            donateLink.href = '{{ route("blood-donation.create") }}';
            donateLink.title = 'Schedule your blood donation';
            donateCard.classList.remove('disabled');
            donateCard.style.opacity = '1';
            headerDonateBtn.href = '{{ route("blood-donation.create") }}';
            headerDonateBtn.title = 'Schedule your blood donation';
        });
}

function loadDashboardStats() {
    fetch('{{ route("user.dashboard-stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-requests').textContent = data.total_requests || 0;
            document.getElementById('total-donations').textContent = data.total_donations || 0;
            document.getElementById('total-appointments').textContent = data.total_appointments || 0;
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
        });
}

function loadRecentActivities() {
    // Load recent blood requests
    fetch('{{ route("user.my-requests") }}?limit=5')
        .then(response => response.text())
        .then(html => {
            // Extract the table body content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const tableBody = doc.querySelector('tbody');
            
            if (tableBody && tableBody.children.length > 0) {
                document.getElementById('recent-requests').innerHTML = tableBody.outerHTML;
            } else {
                document.getElementById('recent-requests').innerHTML = 
                    '<div class="text-center py-4"><p class="text-muted">No recent requests</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading recent requests:', error);
        });
    
    // Load recent blood donations
    fetch('{{ route("user.my-donations") }}?limit=5')
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const tableBody = doc.querySelector('tbody');
            
            if (tableBody && tableBody.children.length > 0) {
                document.getElementById('recent-donations').innerHTML = tableBody.outerHTML;
            } else {
                document.getElementById('recent-donations').innerHTML = 
                    '<div class="text-center py-4"><p class="text-muted">No recent donations</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading recent donations:', error);
        });
    
    // Load upcoming appointments
    fetch('{{ route("user.my-appointments") }}?upcoming=1&limit=5')
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const tableBody = doc.querySelector('tbody');
            
            if (tableBody && tableBody.children.length > 0) {
                document.getElementById('upcoming-appointments').innerHTML = tableBody.outerHTML;
            } else {
                document.getElementById('upcoming-appointments').innerHTML = 
                    '<div class="text-center py-4"><p class="text-muted">No upcoming appointments</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading upcoming appointments:', error);
        });
}

function clearDynamicNotifications() {
    const notificationsContainer = document.getElementById('notifications-container');
    const dynamicNotifications = notificationsContainer.querySelectorAll('.alert[data-dynamic="true"]');
    dynamicNotifications.forEach(notification => {
        notification.remove();
    });
}

function clearAllNotes() {
    // This function is no longer needed as notes are loaded directly from the API
    alert('All persistent notes and dynamic notifications have been cleared.');
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

.bg-danger {
    background-color: #dc3545 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.quick-action-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
    position: relative;
    overflow: hidden;
}

.quick-action-card.disabled::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 193, 7, 0.1), transparent);
    animation: disabled-shine 3s ease-in-out infinite;
}

@keyframes disabled-shine {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.quick-action-card.disabled .card-body {
    color: #6c757d;
    position: relative;
    z-index: 2;
}

.quick-action-card.disabled:hover {
    transform: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.quick-action-card.disabled .bi-heart {
    animation: disabled-heart-pulse 2s ease-in-out infinite;
}

@keyframes disabled-heart-pulse {
    0%, 100% { transform: scale(1); opacity: 0.6; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.btn-outline-danger:not([href]) {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
    position: relative;
    overflow: hidden;
}

.btn-outline-danger:not([href])::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 193, 7, 0.2), transparent);
    animation: button-shine 2s ease-in-out infinite;
}

@keyframes button-shine {
    0% { left: -100%; }
    100% { left: 100%; }
}

.btn-outline-danger:not([href]):hover {
    background-color: transparent;
    border-color: #dc3545;
    color: #dc3545;
}

/* Enhanced Cooldown Design Styles */
.cooldown-container {
    padding: 1.5rem 1rem;
    background: linear-gradient(135deg, #fff5e6 0%, #fff9f0 100%);
    border-radius: 15px;
    border: 2px solid #ffc107;
    position: relative;
    overflow: hidden;
}

.cooldown-container::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 193, 7, 0.1) 0%, transparent 70%);
    animation: pulse-glow 3s ease-in-out infinite;
}

@keyframes pulse-glow {
    0%, 100% { transform: scale(1); opacity: 0.1; }
    50% { transform: scale(1.1); opacity: 0.2; }
}

.cooldown-animation {
    position: relative;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.heart-beat-container {
    position: absolute;
    left: 25%;
    transform: translateX(-50%);
}

.heart-beat {
    font-size: 2.5rem;
    color: #dc3545;
    animation: heart-beat-animation 2s ease-in-out infinite;
}

@keyframes heart-beat-animation {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.1); }
    50% { transform: scale(1.2); }
    75% { transform: scale(1.1); }
}

.cooldown-clock {
    position: absolute;
    right: 25%;
    transform: translateX(50%);
}

.clock-spin {
    font-size: 2.5rem;
    color: #ffc107;
    animation: clock-spin-animation 4s linear infinite;
}

@keyframes clock-spin-animation {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.cooldown-content h4 {
    font-size: 1.25rem;
    color: #856404;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.countdown-card {
    background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 6px 20px rgba(255, 193, 7, 0.3);
    border: 2px solid #ffb300;
    transform: translateY(0);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.countdown-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
}

.countdown-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    line-height: 1;
    margin-bottom: 0.25rem;
}

.countdown-label {
    font-size: 0.8rem;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.cooldown-info {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.next-eligible-date {
    margin-top: 0.75rem;
    padding: 0.75rem;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-radius: 8px;
    border: 1px solid #90caf9;
}

.date-display {
    font-size: 1rem;
    font-weight: bold;
    color: #1976d2;
    margin-top: 0.25rem;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 6px;
    border: 1px solid #90caf9;
}

.cooldown-progress {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.progress {
    background-color: rgba(255, 193, 7, 0.2);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.progress-bar {
    background: linear-gradient(90deg, #ffc107 0%, #ffb300 100%);
    border-radius: 8px;
    transition: width 1s ease-in-out;
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .cooldown-animation {
        height: 70px;
    }
    
    .heart-beat, .clock-spin {
        font-size: 2rem;
    }
    
    .countdown-number {
        font-size: 2rem;
    }
    
    .cooldown-container {
        padding: 1rem 0.75rem;
    }
}

/* Floating particles effect */
.cooldown-container::after {
    content: '🩸';
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 1.25rem;
    animation: float-particle 6s ease-in-out infinite;
    opacity: 0.6;
}

@keyframes float-particle {
    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
    50% { transform: translateY(-15px) rotate(180deg); opacity: 1; }
}

/* Admin and User Note Styles */
.admin-note {
    border-left: 4px solid #dc3545;
    background: linear-gradient(135deg, #fff5f5 0%, #ffe6e6 100%);
    border-color: #dc3545;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.15);
}

.admin-note .alert-heading {
    color: #dc3545;
    font-weight: 600;
}

.user-note {
    border-left: 4px solid #17a2b8;
    background: linear-gradient(135deg, #f0f9ff 0%, #e6f3ff 100%);
    border-color: #17a2b8;
    box-shadow: 0 2px 8px rgba(23, 162, 184, 0.15);
}

.user-note .alert-heading {
    color: #17a2b8;
    font-weight: 600;
}

/* Persistent notification styles */
.alert[data-persistent="true"] {
    position: relative;
    overflow: hidden;
}

.alert[data-persistent="true"]::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: persistent-shine 3s ease-in-out infinite;
}

@keyframes persistent-shine {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Dynamic notification styles */
.alert[data-dynamic="true"] {
    border-left: 4px solid #ffc107;
    background: linear-gradient(135deg, #fffbf0 0%, #fff8e6 100%);
    border-color: #ffc107;
}

/* Helpful Tips & Guide Styles */
.guide-tip {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.guide-tip:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
}

.tip-icon {
    margin-bottom: 0.75rem;
}

.tip-icon i {
    font-size: 1.8rem;
}

.guide-tip h6 {
    color: #343a40;
    margin-bottom: 0.5rem;
}

.guide-tip p {
    font-size: 0.9rem;
    color: #6c757d;
    line-height: 1.4;
}

/* Status Overview Styles */
#pending-requests, #pending-donations, #pending-appointments {
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#pending-requests .badge, #pending-donations .badge, #pending-appointments .badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

#pending-requests small, #pending-donations small, #pending-appointments small {
    font-size: 0.75rem;
    line-height: 1.2;
}
</style>
@endpush
