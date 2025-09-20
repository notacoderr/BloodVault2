@extends('layouts.app')

@section('title', 'Email Verification Required - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope-check me-2"></i>
                        Email Verification Required
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-envelope-check text-info" style="font-size: 4rem;"></i>
                    </div>

                    <h5 class="mb-3">🎉 Registration Successful!</h5>
                    
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Your account has been created successfully!</strong>
                    </div>
                    
                    <p class="text-muted mb-4">
                        We've sent a verification link to your email address. 
                        Please check your inbox and click the verification link to activate your account.
                    </p>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> You must verify your email before you can log in and access your dashboard.
                    </div>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-info-circle me-2"></i>
                                What happens next?
                            </h6>
                            <ol class="text-start text-muted">
                                <li>Check your email for the verification link</li>
                                <li>Click the verification link in the email</li>
                                <li>Your account will be activated</li>
                                <li>You can then log in and access all features</li>
                            </ol>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Go to Login
                        </a>
                        
                        <a href="{{ route('verification.resend.form') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-envelope-arrow-up me-2"></i>
                            Resend Verification Email
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-question-circle me-2"></i>
                        Didn't receive the email?
                    </h6>
                    <ul class="text-muted small">
                        <li>Check your spam/junk folder</li>
                        <li>Make sure you entered the correct email address</li>
                        <li>Wait a few minutes for the email to arrive</li>
                        <li>Use the "Resend Verification Email" button above</li>
                    </ul>
                </div>
            </div>

            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-shield-check me-2"></i>
                        Why Email Verification?
                    </h6>
                    <p class="card-text text-muted small">
                        Email verification helps us ensure that:
                    </p>
                    <ul class="text-muted small">
                        <li>You provide a valid email address</li>
                        <li>We can communicate important updates</li>
                        <li>Your account is secure and legitimate</li>
                        <li>We maintain the integrity of our blood donation system</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
