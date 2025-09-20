@extends('layouts.app')

@section('title', 'Resend Verification Email - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope-arrow-up me-2"></i>
                        Resend Verification Email
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <i class="bi bi-envelope-exclamation text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-2">Need a new verification email?</h5>
                        <p class="text-muted">Enter your email address and we'll send you a new verification link.</p>
                    </div>

                    <form action="{{ route('verification.resend') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required 
                                   placeholder="Enter your registered email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-envelope-arrow-up me-2"></i>
                                Send Verification Email
                            </button>
                            
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-question-circle me-2"></i>
                        Having trouble?
                    </h6>
                    <ul class="text-muted small">
                        <li>Make sure you entered the correct email address</li>
                        <li>Check your spam/junk folder</li>
                        <li>Wait a few minutes for the email to arrive</li>
                        <li>If the problem persists, contact support</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
