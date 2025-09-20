@extends('layouts.app')

@section('title', 'My Blood Donations - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-heart me-2"></i>
                        My Blood Donations
                    </h4>
                    <a href="{{ route('blood-donation.create') }}" class="btn btn-light">
                        <i class="bi bi-plus-circle me-2"></i>
                        New Donation
                    </a>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($bloodDonations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Blood Type</th>
                                        <th>Donation Date</th>
                                        <th>Status</th>
                                        <th>Screening</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bloodDonations as $donation)
                                    <tr>
                                        <td>
                                            <span class="badge bg-success">{{ $donation->blood_type }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'completed' => 'info',
                                                    'cancelled' => 'secondary',
                                                    'rejected' => 'danger'
                                                ];
                                                $statusColor = $statusColors[$donation->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ ucfirst($donation->status) }}
                                            </span>
                                            @if($donation->status === 'rejected' && $donation->notes)
                                                <div class="mt-1">
                                                    <small class="text-danger">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        <strong>Reason:</strong> {{ $donation->notes }}
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($donation->screening_answers)
                                                <span class="badge bg-info">Completed</span>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.donation-details', $donation->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($donation->status === 'pending')
                                                    <form action="{{ route('blood-donation.cancel', $donation->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to cancel this donation?')">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Cancel Donation">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bloodDonations->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-heart text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No Blood Donations Yet</h5>
                            <p class="text-muted">You haven't registered for any blood donations yet.</p>
                            <a href="{{ route('blood-donation.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>
                                Register Your First Donation
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .badge {
        font-size: 0.8rem;
    }
</style>
@endpush
