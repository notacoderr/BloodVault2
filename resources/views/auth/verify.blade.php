@extends('layouts.app')

@section('title', 'Verify Email - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope-exclamation me-2"></i>
                        Verify Your Email Address
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-envelope-check text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <h5 class="mb-3">Before proceeding, please check your email for a verification link.</h5>
                    
                    <p class="text-muted mb-4">
                        We've sent a verification link to <strong>{{ auth()->user()->email }}</strong>. 
                        Please click the link in that email to verify your account.
                    </p>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Didn't receive the email?</strong> Check your spam folder or request a new verification email.
                    </div>

                    <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-envelope-arrow-up me-2"></i>
                            Resend Verification Email
                        </button>
                    </form>

                    <div class="mt-4">
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </a>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-question-circle me-2"></i>
                        Need Help?
                    </h6>
                    <p class="card-text text-muted mb-2">
                        If you're having trouble verifying your email:
                    </p>
                    <ul class="text-muted small">
                        <li>Check your spam/junk folder</li>
                        <li>Make sure you entered the correct email address</li>
                        <li>Wait a few minutes for the email to arrive</li>
                        <li>Contact support if the problem persists</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh page every 30 seconds to check for verification
    setTimeout(function() {
        location.reload();
    }, 30000);
});
</script>
@endpush
