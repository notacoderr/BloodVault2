@extends('layouts.app')

@section('title', 'About - Life Vault')

@section('content')
<!-- Page Header -->
<div class="text-center mb-5">
    <h1 class="display-4 fw-bold text-danger mb-3">
        <i class="bi bi-info-circle me-3"></i>
        About Life Vault
    </h1>
    <p class="lead text-muted">Saving lives through efficient blood donation and management</p>
</div>

<!-- Mission & Vision -->
<div class="row mb-5">
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-bullseye text-danger" style="font-size: 2rem;"></i>
                </div>
                <h4 class="card-title">Our Mission</h4>
                <p class="card-text">To provide a reliable, efficient, and user-friendly blood bank management system that connects donors with recipients, ensuring timely access to safe blood products for all who need them.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-eye text-danger" style="font-size: 2rem;"></i>
                </div>
                <h4 class="card-title">Our Vision</h4>
                <p class="card-text">To become the leading blood bank management platform that revolutionizes how blood donation and transfusion services are coordinated, making healthcare more accessible and efficient.</p>
            </div>
        </div>
    </div>
</div>

<!-- What We Do -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h3 class="mb-0 text-center">
                    <i class="bi bi-gear-wide-connected me-2"></i>
                    What We Do
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-heart text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>Blood Donation Management</h5>
                            <p class="text-muted">Streamline the blood donation process from scheduling to completion, ensuring donor safety and satisfaction.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-list-check text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>Blood Request Processing</h5>
                            <p class="text-muted">Efficiently process blood requests and match them with available inventory to save lives quickly.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-calendar-check text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>Appointment Scheduling</h5>
                            <p class="text-muted">Easy-to-use appointment booking system for donations, screenings, and consultations.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-database text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>Inventory Management</h5>
                            <p class="text-muted">Comprehensive blood inventory tracking with expiration monitoring and stock alerts.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-shield-check text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>Quality Assurance</h5>
                            <p class="text-muted">Maintain high standards of blood safety through rigorous screening and testing protocols.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-graph-up text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5>Analytics & Reporting</h5>
                            <p class="text-muted">Comprehensive reporting and analytics to optimize operations and improve service delivery.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Our Values -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h3 class="mb-0 text-center">
                    <i class="bi bi-star me-2"></i>
                    Our Core Values
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-heart-fill text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Compassion</h6>
                            <p class="small text-muted">We care deeply about every life we touch through our services.</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-shield-check text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Safety</h6>
                            <p class="small text-muted">We prioritize the safety and well-being of all donors and recipients.</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-lightning text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Efficiency</h6>
                            <p class="small text-muted">We strive for excellence in every process to save more lives.</p>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-people text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <h6>Community</h6>
                            <p class="small text-muted">We build strong partnerships to serve our community better.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Technology Stack -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h3 class="mb-0 text-center">
                    <i class="bi bi-cpu me-2"></i>
                    Technology & Innovation
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h5><i class="bi bi-code-slash text-danger me-2"></i>Modern Web Technologies</h5>
                        <p class="text-muted">Built with Laravel framework, Bootstrap 5, and modern JavaScript to provide a responsive and user-friendly experience across all devices.</p>
                        
                        <h5><i class="bi bi-database text-danger me-2"></i>Secure Data Management</h5>
                        <p class="text-muted">Robust database design with proper security measures to protect sensitive medical information and ensure data integrity.</p>
                    </div>

                    <div class="col-md-6 mb-4">
                        <h5><i class="bi bi-phone text-danger me-2"></i>Mobile-First Design</h5>
                        <p class="text-muted">Responsive design that works seamlessly on desktop, tablet, and mobile devices, ensuring accessibility for all users.</p>
                        
                        <h5><i class="bi bi-graph-up text-danger me-2"></i>Real-Time Updates</h5>
                        <p class="text-muted">Live updates and notifications to keep users informed about their requests, donations, and appointments in real-time.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h3 class="mb-0 text-center">
                    <i class="bi bi-people-fill me-2"></i>
                    Our Team
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <p class="lead">Life Vault is developed and maintained by a dedicated team of healthcare professionals and technology experts committed to improving blood bank services.</p>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-person-badge text-danger" style="font-size: 2rem;"></i>
                            </div>
                            <h6>Healthcare Experts</h6>
                            <p class="small text-muted">Medical professionals who understand the critical needs of blood banking and transfusion services.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-laptop text-danger" style="font-size: 2rem;"></i>
                            </div>
                            <h6>Technology Team</h6>
                            <p class="small text-muted">Skilled developers and engineers who build robust, scalable, and secure software solutions.</p>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="text-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-headset text-danger" style="font-size: 2rem;"></i>
                            </div>
                            <h6>Support Staff</h6>
                            <p class="small text-muted">Dedicated support team providing assistance and training to ensure smooth system operation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Information -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h3 class="mb-0 text-center">
                    <i class="bi bi-envelope me-2"></i>
                    Get In Touch
                </h3>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h5><i class="bi bi-geo-alt text-danger me-2"></i>Location</h5>
                        <p class="text-muted">Philippines<br>BSIT-Team6</p>
                        
                        <h5><i class="bi bi-telephone text-danger me-2"></i>Contact</h5>
                        <p class="text-muted">For support and inquiries, please contact our team through the system.</p>
                    </div>

                    <div class="col-md-6 mb-4">
                        <h5><i class="bi bi-clock text-danger me-2"></i>Support Hours</h5>
                        <p class="text-muted">24/7 System Availability<br>Technical Support: Business Hours</p>
                        
                        <h5><i class="bi bi-shield-check text-danger me-2"></i>Security</h5>
                        <p class="text-muted">Your data security and privacy are our top priorities. We use industry-standard encryption and security measures.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="text-center py-5">
    <h3 class="mb-4">Ready to Make a Difference?</h3>
    <p class="lead mb-4">Join Life Vault today and be part of a community dedicated to saving lives through blood donation.</p>
    <div class="d-flex gap-3 justify-content-center">
        @guest
            <a href="{{ route('register') }}" class="btn btn-danger btn-lg px-5">
                <i class="bi bi-person-plus me-2"></i>Get Started
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-danger btn-lg px-5">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </a>
        @else
            <a href="{{ route('user.dashboard') }}" class="btn btn-danger btn-lg px-5">
                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
            </a>
            <a href="{{ route('blood-donation.create') }}" class="btn btn-outline-danger btn-lg px-5">
                <i class="bi bi-heart me-2"></i>Donate Blood
            </a>
        @endguest
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
}

.bg-danger.bg-opacity-10 {
    background-color: rgba(220, 53, 69, 0.1) !important;
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
</style>
@endpush
