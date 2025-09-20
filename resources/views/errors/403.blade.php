@extends('layouts.app')

@section('title', 'Access Denied - Life Vault')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-shield-x text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="text-danger mb-3">Account Deactivated</h2>
                    
                    <p class="text-muted mb-4">
                        Your account has been deactivated by an administrator. 
                        You no longer have access to this system.
                    </p>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>What happened?</strong><br>
                        An administrator has deactivated your account, which means you cannot:
                        <ul class="text-start mt-2 mb-0">
                            <li>Access your dashboard</li>
                            <li>Make blood requests</li>
                            <li>Schedule appointments</li>
                            <li>View your profile</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <a href="/" class="btn btn-primary me-2">
                            <i class="bi bi-house me-2"></i>Go to Homepage
                        </a>
                        <a href="/login" class="btn btn-outline-secondary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Try to Login
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <small class="text-muted">
                            If you believe this is an error, please contact an administrator.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
