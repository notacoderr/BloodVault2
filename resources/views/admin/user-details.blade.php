@extends('layouts.app')

@section('title', 'User Details - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-person text-primary me-2"></i>
                        User Details
                    </h2>
                    <p class="text-muted mb-0">View and manage user account information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.user.edit', $user->USER_ID) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </a>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                        <i class="bi bi-trash me-2"></i>Delete User
                    </button>
                </div>
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Please fix the following errors:
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- User Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        User Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">User ID:</label>
                            <p class="mb-0">#{{ $user->USER_ID }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Name:</label>
                            <p class="mb-0">{{ $user->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Email:</label>
                            <p class="mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">User Type:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $user->usertype === 'admin' ? 'danger' : ($user->usertype === 'donor' ? 'success' : 'warning') }}">
                                    {{ ucfirst($user->usertype) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Blood Type:</label>
                            <p class="mb-0">
                                @if($user->bloodtype)
                                    <span class="badge bg-danger">{{ $user->bloodtype }}</span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Contact:</label>
                            <p class="mb-0">{{ $user->contact ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Date of Birth:</label>
                            <p class="mb-0">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('M d, Y') : 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Sex:</label>
                            <p class="mb-0">{{ $user->sex ? ucfirst($user->sex) : 'Not specified' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Account Verified:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $user->is_verified ? 'success' : 'danger' }}">
                                    {{ $user->is_verified ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Member Since:</label>
                            <p class="mb-0">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Status:</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $user->is_verified ? 'success' : 'warning' }}">
                                    {{ $user->is_verified ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    @if($user->address)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Address:</label>
                        <p class="mb-0">{{ $user->address }}</p>
                    </div>
                    @endif
                    
                    @if($user->city || $user->province)
                    <div class="row">
                        @if($user->city)
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">City:</label>
                            <p class="mb-0">{{ $user->city }}</p>
                        </div>
                        @endif
                        @if($user->province)
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted">Province:</label>
                            <p class="mb-0">{{ $user->province }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Update User Type -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('admin.user.edit', $user->USER_ID) }}" class="btn btn-primary w-100">
                                <i class="bi bi-pencil me-2"></i>
                                Edit User Details
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <form action="{{ route('admin.user-status', $user->USER_ID) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to toggle this user\'s verification status?')">
                                    <i class="bi bi-toggle-{{ $user->is_verified ? 'on' : 'off' }} me-2"></i>
                                    {{ $user->is_verified ? 'Deactivate' : 'Activate' }} User
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-list-check text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <h3 class="mb-1">{{ $user->bloodRequests->count() }}</h3>
                            <p class="text-muted mb-0">Blood Requests</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-heart text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <h3 class="mb-1">{{ $user->bloodDonations->count() }}</h3>
                            <p class="text-muted mb-0">Blood Donations</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-calendar-check text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <h3 class="mb-1">{{ $user->appointments->count() }}</h3>
                            <p class="text-muted mb-0">Appointments</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-clock text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <h3 class="mb-1">{{ $user->bloodRequests->where('status', 'pending')->count() }}</h3>
                            <p class="text-muted mb-0">Pending Requests</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Blood Requests -->
            @if($user->bloodRequests->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2"></i>
                        Recent Blood Requests
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Blood Type</th>
                                    <th>Units</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->bloodRequests->take(5) as $request)
                                <tr>
                                    <td>{{ $request->request_date->format('M d, Y') }}</td>
                                    <td><span class="badge bg-danger">{{ $request->blood_type }}</span></td>
                                    <td>{{ $request->units_needed }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->urgency === 'critical' ? 'danger' : ($request->urgency === 'high' ? 'warning' : ($request->urgency === 'medium' ? 'info' : 'success')) }}">
                                            {{ ucfirst($request->urgency) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'info' : ($request->status === 'completed' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'secondary'))) }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.blood-request-details', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Blood Donations -->
            @if($user->bloodDonations->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-heart me-2"></i>
                        Recent Blood Donations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Blood Type</th>
                                    <th>Screening</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->bloodDonations->take(5) as $donation)
                                <tr>
                                    <td>{{ $donation->created_at->format('M d, Y') }}</td>
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
                                            {{ ucfirst($donation->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Appointments -->
            @if($user->appointments->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Recent Appointments
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->appointments->take(5) as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $appointment->appointment_type)) }}</td>
                                    <td>{{ $appointment->time_slot }}</td>
                                    <td>
                                        <span class="badge bg-{{ $appointment->status === 'pending' ? 'warning' : ($appointment->status === 'confirmed' ? 'info' : ($appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'secondary'))) }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm User Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong>{{ $user->name ?? $user->email }}</strong>?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will permanently remove the user account and all associated data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.user-delete', $user->USER_ID) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
