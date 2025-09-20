@extends('layouts.app')

@section('title', 'My Blood Requests - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-droplet me-2"></i>
                        My Blood Requests
                    </h4>
                    <a href="{{ route('blood-request.create') }}" class="btn btn-light">
                        <i class="bi bi-plus-circle me-2"></i>
                        New Request
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

                    @if($bloodRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Blood Type</th>
                                        <th>Units</th>
                                        <th>Urgency</th>
                                        <th>Required Date</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bloodRequests as $request)
                                    <tr>
                                        <td>
                                            <span class="badge bg-danger">{{ $request->blood_type }}</span>
                                        </td>
                                        <td>{{ $request->units_needed }}</td>
                                        <td>
                                            @php
                                                $urgencyColors = [
                                                    'low' => 'success',
                                                    'medium' => 'warning',
                                                    'high' => 'danger',
                                                    'critical' => 'dark'
                                                ];
                                                $urgencyColor = $urgencyColors[$request->urgency] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $urgencyColor }}">
                                                {{ ucfirst($request->urgency) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($request->request_date)->format('M d, Y') }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'completed' => 'info',
                                                    'cancelled' => 'secondary',
                                                    'rejected' => 'danger'
                                                ];
                                                $statusColor = $statusColors[$request->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                            @if($request->status === 'rejected' && $request->admin_notes)
                                                <div class="mt-1">
                                                    <small class="text-danger">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        <strong>Reason:</strong> {{ $request->admin_notes }}
                                                    </small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.request-details', $request->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($request->status === 'pending')
                                                    <form action="{{ route('user.cancel-request', $request->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to cancel this request?')">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Cancel Request">
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
                            {{ $bloodRequests->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-droplet text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No Blood Requests Yet</h5>
                            <p class="text-muted">You haven't made any blood requests yet.</p>
                            <a href="{{ route('blood-request.create') }}" class="btn btn-danger">
                                <i class="bi bi-plus-circle me-2"></i>
                                Make Your First Request
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
