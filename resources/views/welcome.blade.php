@extends('layouts.app')

@section('title', 'Welcome - Life Vault Blood Bank')

@section('content')
<!-- Hero Section -->
<div class="hero-section text-center py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4 hero-title">
                    <i class="bi bi-droplet-fill me-3"></i>
                    Life Vault
                </h1>
                <p class="lead mb-4 hero-subtitle">Saving lives through efficient blood donation and management. Join us in making a difference in healthcare.</p>
                <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-person-plus me-2"></i>Get Started
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg px-4 hero-btn-primary">
                            <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                        </a>
                        <a href="{{ route('blood-request.create') }}" class="btn btn-outline-light btn-lg px-4 hero-btn-secondary">
                            <i class="bi bi-plus-circle me-2"></i>Request Blood
                        </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="bi bi-droplet-fill" style="font-size: 8rem; opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="row mb-5">
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-heart text-danger" style="font-size: 2rem;"></i>
                </div>
                <h5 class="card-title">Blood Donation</h5>
                <p class="card-text">Schedule your blood donation appointment and help save lives. Our screening process ensures safety for all.</p>
                @auth
                    <a href="{{ route('blood-donation.create') }}" class="btn btn-outline-danger">
                        <i class="bi bi-calendar-plus me-2"></i>Schedule Donation
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-outline-danger">
                        <i class="bi bi-person-plus me-2"></i>Register to Donate
                    </a>
                @endauth
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-plus-circle text-danger" style="font-size: 2rem;"></i>
                </div>
                <h5 class="card-title">Blood Requests</h5>
                <p class="p class="card-text">Request blood units when you need them. Our system matches your requirements with available inventory.</p>
                @auth
                    <a href="{{ route('blood-request.create') }}" class="btn btn-outline-danger">
                        <i class="bi bi-plus-circle me-2"></i>Request Blood
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-outline-danger">
                        <i class="bi bi-person-plus me-2"></i>Register to Request
                    </a>
                @endauth
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-calendar-check text-danger" style="font-size: 2rem;"></i>
                </div>
                <h5 class="card-title">Appointment Booking</h5>
                <p class="card-text">Book appointments for donations, screenings, or consultations. Manage your schedule efficiently.</p>
                @auth
                    <a href="{{ route('appointment.create') }}" class="btn btn-outline-danger">
                        <i class="bi bi-calendar-plus me-2"></i>Book Appointment
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-outline-danger">
                        <i class="bi bi-person-plus me-2"></i>Register to Book
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Our Impact</h3>
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="border-end">
                            <h2 class="text-danger fw-bold">1000+</h2>
                            <p class="text-muted mb-0">Lives Saved</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border-end">
                            <h2 class="text-danger fw-bold">500+</h2>
                            <p class="text-muted mb-0">Blood Donors</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border-end">
                            <h2 class="text-danger fw-bold">2000+</h2>
                            <p class="text-muted mb-0">Units Collected</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div>
                            <h2 class="text-danger fw-bold">24/7</h2>
                            <p class="text-muted mb-0">Emergency Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="row mb-5">
    <div class="col-12">
        <h3 class="text-center mb-4">How It Works</h3>
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <span class="fw-bold text-danger">1</span>
                </div>
                <h6>Register</h6>
                <p class="small text-muted">Create your account and complete your profile</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <span class="fw-bold text-danger">2</span>
                </div>
                <h6>Schedule</h6>
                <p class="small text-muted">Book your donation or request appointment</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <span class="fw-bold text-danger">3</span>
                </div>
                <h6>Process</h6>
                <p class="small text-muted">Complete screening and donation process</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <span class="fw-bold text-danger">4</span>
                </div>
                <h6>Track</h6>
                <p class="small text-muted">Monitor your requests and donations</p>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="text-center py-5">
    <h3 class="mb-4">Ready to Make a Difference?</h3>
    <p class="lead mb-4">Join thousands of donors and recipients who trust Life Vault for their blood banking needs.</p>
    @guest
        <a href="{{ route('register') }}" class="btn btn-danger btn-lg px-5">
            <i class="bi bi-person-plus me-2"></i>Join Life Vault Today
        </a>
    @else
        <a href="{{ route('user.dashboard') }}" class="btn btn-danger btn-lg px-5">
            <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
        </a>
    @endguest
</div>
@endsection

@push('styles')
<style>
.hero-section {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(220, 53, 69, 0.3);
    animation: heroGlow 3s ease-in-out infinite alternate;
    color: white !important;
}

@keyframes heroGlow {
    0% {
        box-shadow: 0 20px 40px rgba(220, 53, 69, 0.3);
    }
    100% {
        box-shadow: 0 20px 40px rgba(220, 53, 69, 0.5);
    }
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
    z-index: 1;
}

.hero-section::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    z-index: 1;
}

/* Ensure buttons are clickable and styled beautifully */
.hero-section .btn {
    position: relative;
    z-index: 10;
    cursor: pointer;
    pointer-events: auto;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 12px;
    text-transform: uppercase;
    font-size: 0.9rem;
}

.hero-section .d-flex {
    position: relative;
    z-index: 10;
}

/* Ensure content is visible */
.hero-section .container {
    position: relative;
    z-index: 10;
}

.hero-section .row {
    position: relative;
    z-index: 10;
}

.hero-section .col-lg-6 {
    position: relative;
    z-index: 10;
}

/* Primary button styling */
.hero-btn-primary {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 2px solid #ffffff;
    color: #dc3545;
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

.hero-btn-primary:hover {
    background: linear-gradient(135deg, #ffffff 0%, #e9ecef 100%);
    border-color: #ffffff;
    color: #c82333;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.4);
}

.hero-btn-primary:active {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

/* Secondary button styling */
.hero-btn-secondary {
    background: transparent;
    border: 2px solid #ffffff;
    color: #ffffff;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
}

.hero-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: #ffffff;
    color: #ffffff;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(15px);
}

.hero-btn-secondary:active {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
}

/* Button icons */
.hero-section .btn i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.hero-section .btn:hover i {
    transform: scale(1.1);
}

/* Typography improvements */
.hero-title {
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    letter-spacing: 1px;
    color: white !important;
    position: relative;
    z-index: 10;
}

.hero-subtitle {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    font-weight: 400;
    line-height: 1.6;
    color: white !important;
    position: relative;
    z-index: 10;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .hero-section {
        border-radius: 15px;
        margin: 0 10px;
    }
    
    .hero-section .btn {
        font-size: 0.85rem;
        padding: 0.75rem 1.5rem;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
}

.border-end {
    border-right: 2px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .border-end {
        border-right: none !important;
        border-bottom: 2px solid #dee2e6 !important;
        padding-bottom: 1rem;
    }
}
</style>
@endpush


