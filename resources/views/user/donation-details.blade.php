@extends('layouts.app')

@section('title', 'Donation Details - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-heart me-2"></i>
                        Blood Donation Details
                    </h4>
                    <a href="{{ route('user.my-donations') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Donations
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Donor Name</label>
                            <div class="fs-6">{{ $bloodDonation->donor_name }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Donor Email</label>
                            <div class="fs-6">{{ $bloodDonation->donor_email }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Blood Type</label>
                            <div>
                                <span class="badge bg-success fs-6">{{ $bloodDonation->blood_type }}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Donation Date</label>
                            <div class="fs-6">{{ \Carbon\Carbon::parse($bloodDonation->donation_date)->format('F d, Y') }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'completed' => 'info',
                                        'cancelled' => 'secondary',
                                        'rejected' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$bloodDonation->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }} fs-6">
                                    {{ ucfirst($bloodDonation->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Registration Date</label>
                            <div class="fs-6">{{ \Carbon\Carbon::parse($bloodDonation->created_at)->format('F d, Y \a\t g:i A') }}</div>
                        </div>
                    </div>

                    @if($bloodDonation->screening_answers)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Health Screening Status</label>
                            <div>
                                <span class="badge bg-info fs-6">Completed</span>
                            </div>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label fw-bold">Health Screening Status</label>
                            <div>
                                <span class="badge bg-warning fs-6">Pending</span>
                            </div>
                        </div>
                    @endif

                    @if($bloodDonation->notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Additional Notes</label>
                            <div class="fs-6">{{ $bloodDonation->notes }}</div>
                        </div>
                    @endif

                    @if($bloodDonation->screening_answers)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Screening Responses</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    @php
                                        $screeningData = json_decode($bloodDonation->screening_answers, true);
                                        $screeningQuestions = [
                                            'feels_healthy' => 'Feeling healthy and well today?',
                                            'taking_antibiotic' => 'Currently taking an antibiotic?',
                                            'medfor_infection' => 'Currently taking medication for infection?',
                                            'currently_pregnant' => 'Pregnant now?',
                                            'took_aspirin' => 'Taken aspirin in past 48 hours?',
                                            'donated_blood' => 'Donated blood in past 8 weeks?',
                                            'had_vaccine' => 'Had vaccinations in past 8 weeks?',
                                            'contact_w_smallpox' => 'Contact with smallpox vaccine recipients?',
                                            'med_for_hiv' => 'Taken HIV prevention medication?',
                                            'sex_new_partner' => 'Sexual contact with new partner?',
                                            'sex_more_partner' => 'Sexual contact with multiple partners?',
                                            'sex_hiv_positive' => 'Sexual contact with HIV+ partner?',
                                            'sex_get_payment' => 'Received payment for sex?',
                                            'sex_partner_get_payment' => 'Partner received payment for sex?',
                                            'used_injected_drugs' => 'Used injection drugs?',
                                            'sex_partner_used_injected_drugs' => 'Partner used injection drugs?',
                                            'had_syphilis_gonorrhea' => 'Had syphilis or gonorrhea?',
                                            'sex_partner_has_hepatitis' => 'Sexual contact with hepatitis patient?',
                                            'live_with_hepatitis_patient' => 'Lived with hepatitis patient?',
                                            'accidental_needle_stick' => 'Had accidental needle stick?',
                                            'contact_with_others_blood' => 'Contact with others\' blood?',
                                            'had_tattoo' => 'Had tattoo?',
                                            'had_piercing' => 'Had piercing?',
                                            'had_transfusion' => 'Had blood transfusion?',
                                            'had_transplant' => 'Had organ transplant?',
                                            'had_graft' => 'Had tissue graft?'
                                        ];
                                    @endphp
                                    
                                    @foreach($screeningQuestions as $key => $question)
                                        @if(isset($screeningData[$key]))
                                            <div class="row mb-2">
                                                <div class="col-md-8">
                                                    <small class="text-muted">{{ $question }}</small>
                                                </div>
                                                <div class="col-md-4">
                                                    @if($screeningData[$key] === 'yes')
                                                        <span class="badge bg-danger">Yes</span>
                                                    @else
                                                        <span class="badge bg-success">No</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($bloodDonation->status === 'pending')
                                <form action="{{ route('blood-donation.cancel', $bloodDonation->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to cancel this donation?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-2"></i>
                                        Cancel Donation
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div>
                            <a href="{{ route('user.my-donations') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to Donations
                            </a>
                            @if($bloodDonation->status === 'pending')
                                <a href="{{ route('blood-donation.edit', $bloodDonation->id) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>
                                    Edit Donation
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-label {
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    
    .fs-6 {
        font-size: 1rem !important;
    }
    
    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    
    .card.bg-light {
        border: 1px solid #dee2e6;
    }
</style>
@endpush
