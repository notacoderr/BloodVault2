@extends('layouts.app')

@section('title', 'Page Not Found - Life Vault')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 text-center">
        <div class="error-page">
            <!-- 404 Icon -->
            <div class="error-icon mb-4">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 8rem;"></i>
            </div>

            <!-- Error Message -->
            <h1 class="display-1 fw-bold text-muted mb-3">404</h1>
            <h2 class="h3 mb-3">Page Not Found</h2>
            <p class="lead text-muted mb-4">
                Oops! The page you're looking for doesn't exist or has been moved.
            </p>

            <!-- Helpful Links -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-house text-primary mb-2" style="font-size: 2rem;"></i>
                            <h6>Go Home</h6>
                            <p class="small text-muted mb-2">Return to the main page</p>
                            <a href="{{ url('/') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-house me-1"></i>Home
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-search text-info mb-2" style="font-size: 2rem;"></i>
                            <h6>Search</h6>
                            <p class="small text-muted mb-2">Find what you're looking for</p>
                            <button class="btn btn-outline-info btn-sm" onclick="focusSearch()">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-4">
                <h5 class="mb-3">Quick Actions</h5>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus me-2"></i>Register
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                        <a href="{{ route('blood-request.create') }}" class="btn btn-outline-danger">
                            <i class="bi bi-plus-circle me-2"></i>Request Blood
                        </a>
                        <a href="{{ route('blood-donation.create') }}" class="btn btn-outline-success">
                            <i class="bi bi-heart me-2"></i>Donate Blood
                        </a>
                    @endguest
                </div>
            </div>

            <!-- Help Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Need Help?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        If you believe this is an error or need assistance, please:
                    </p>
                    <ul class="text-start text-muted">
                        <li>Check the URL for typos</li>
                        <li>Use the navigation menu above</li>
                        <li>Go back to the previous page</li>
                        <li>Contact support if the problem persists</li>
                    </ul>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-4">
                <button class="btn btn-outline-secondary" onclick="history.back()">
                    <i class="bi bi-arrow-left me-2"></i>Go Back
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.error-page {
    padding: 2rem 0;
}

.error-icon {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-info {
    color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-success {
    color: #198754;
    border-color: #198754;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
function focusSearch() {
    // Try to find a search input on the page
    const searchInput = document.querySelector('input[type="search"], input[name="search"], input[placeholder*="search"]');
    if (searchInput) {
        searchInput.focus();
        searchInput.select();
    } else {
        // If no search input found, redirect to home page
        window.location.href = '{{ url("/") }}';
    }
}

// Add keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        history.back();
    } else if (e.key === 'Home') {
        window.location.href = '{{ url("/") }}';
    }
});
</script>
@endpush
