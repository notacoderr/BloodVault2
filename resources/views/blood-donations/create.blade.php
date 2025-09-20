@extends('layouts.app')

@section('title', 'Donate Blood - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-heart me-2"></i>
                        Donate Blood
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

                    @if(!$canDonate)
                        <div class="cooldown-warning-container">
                            <div class="cooldown-header text-center mb-4">
                                <div class="cooldown-icon-container mb-3">
                                    <div class="cooldown-icon-wrapper">
                                        <i class="bi bi-heart-pulse cooldown-heart"></i>
                                        <i class="bi bi-clock cooldown-clock-icon"></i>
                                    </div>
                                </div>
                                <h3 class="text-warning fw-bold mb-2">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Donation Cooldown Active
                                </h3>
                                <p class="text-muted">Your safety is our priority</p>
                            </div>
                            
                            <div class="cooldown-content">
                                <div class="cooldown-timer-section text-center mb-4">
                                    <div class="countdown-display">
                                        <div class="countdown-circle">
                                            <div class="countdown-number">{{ $remainingDays }}</div>
                                            <div class="countdown-unit">Days</div>
                                        </div>
                                        <div class="countdown-text">
                                            <span class="text-warning fw-bold">Remaining</span> before your next donation
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="cooldown-info-section mb-4">
                                    <div class="info-card">
                                        <div class="info-header">
                                            <i class="bi bi-calendar-event text-info me-2"></i>
                                            <strong>Next Eligible Date</strong>
                                        </div>
                                        <div class="date-highlight">{{ $nextEligibleDate->format('F j, Y') }}</div>
                                    </div>
                                    
                                    <div class="info-card mt-3">
                                        <div class="info-header">
                                            <i class="bi bi-info-circle text-primary me-2"></i>
                                            <strong>Why the Wait?</strong>
                                        </div>
                                        <p class="text-muted mb-0">
                                            Blood donation requires a 56-day cooldown period to ensure donor safety and maintain blood quality standards. This follows medical guidelines and protects both donors and recipients.
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="cooldown-progress-section">
                                    <div class="progress-header mb-2">
                                        <small class="text-muted">Cooldown Progress</small>
                                    </div>
                                    <div class="progress" style="height: 12px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ max(0, 100 - ($remainingDays / 56 * 100)) }}%" 
                                             aria-valuenow="{{ max(0, 100 - ($remainingDays / 56 * 100)) }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="progress-stats mt-2">
                                        <small class="text-muted">
                                            {{ max(0, 56 - $remainingDays) }} of 56 days completed
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="cooldown-actions text-center mt-4">
                                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary me-2">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Back to Dashboard
                                    </a>
                                    <a href="{{ route('user.my-donations') }}" class="btn btn-outline-info">
                                        <i class="bi bi-heart me-2"></i>
                                        View My Donations
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Thank you for considering blood donation!</strong> Your donation can save up to 3 lives. Please fill out the form below to register as a donor.
                        </div>
                    @endif

                    @if($canDonate)
                    <form action="{{ route('blood-donation.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="donor_name" class="form-label">Donor Name</label>
                                <input type="text" class="form-control" id="donor_name" name="donor_name" value="{{ old('donor_name', auth()->user()->name) }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="donor_email" class="form-label">Donor Email</label>
                                <input type="email" class="form-control" id="donor_email" name="donor_email" value="{{ old('donor_email', auth()->user()->email) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Blood Type</label>
                                <select class="form-select" id="blood_type" name="blood_type" required>
                                    <option value="">Select Blood Type</option>
                                    <option value="A+" {{ old('blood_type', auth()->user()->bloodtype) == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_type', auth()->user()->bloodtype) == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_type', auth()->user()->bloodtype) == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_type', auth()->user()->bloodtype) == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_type', auth()->user()->bloodtype) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_type', auth()->user()->bloodtype) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_type', auth()->user()->bloodtype) == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_type', auth()->user()->bloodtype) == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="donation_date" class="form-label">Preferred Donation Date</label>
                                <input type="date" class="form-control" id="donation_date" name="donation_date" value="{{ old('donation_date') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any special conditions, medications, or additional information...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-clipboard-check me-2"></i>
                                    Health Screening Questions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Important:</strong> In compliance with DOH, Ph-RC, and WHO policies, all donors are subject to pre-screening questions and must answer truthfully. This ensures the safety of donors, recipients, and health workers.
                                </div>

                                <!-- ARE YOU -->
                                <div class="mb-4">
                                    <h6 class="text-danger fw-bold mb-3">ARE YOU</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Feeling healthy and well today?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_feels_healthy" id="feels_healthy_yes" value="yes" required>
                                                <label class="form-check-label" for="feels_healthy_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_feels_healthy" id="feels_healthy_no" value="no">
                                                <label class="form-check-label" for="feels_healthy_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Currently taking an antibiotic?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_taking_antibiotic" id="taking_antibiotic_yes" value="yes" required>
                                                <label class="form-check-label" for="taking_antibiotic_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_taking_antibiotic" id="taking_antibiotic_no" value="no">
                                                <label class="form-check-label" for="taking_antibiotic_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Currently taking any other medication for an infection?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_medfor_infection" id="medfor_infection_yes" value="yes" required>
                                                <label class="form-check-label" for="medfor_infection_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_medfor_infection" id="medfor_infection_no" value="no">
                                                <label class="form-check-label" for="medfor_infection_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Pregnant now?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_currently_pregnant" id="currently_pregnant_yes" value="yes" required>
                                                <label class="form-check-label" for="currently_pregnant_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_currently_pregnant" id="currently_pregnant_no" value="no">
                                                <label class="form-check-label" for="currently_pregnant_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- IN THE PAST 48 HOURS -->
                                <div class="mb-4">
                                    <h6 class="text-danger fw-bold mb-3">IN THE PAST 48 HOURS</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Have you taken aspirin or anything that has aspirin?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_took_aspirin" id="took_aspirin_yes" value="yes" required>
                                                <label class="form-check-label" for="took_aspirin_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_took_aspirin" id="took_aspirin_no" value="no">
                                                <label class="form-check-label" for="took_aspirin_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- IN THE PAST 8 WEEKS -->
                                <div class="mb-4">
                                    <h6 class="text-danger fw-bold mb-3">IN THE PAST 8 WEEKS, HAVE YOU</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Donated blood, platelets or plasma?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_donated_blood" id="donated_blood_yes" value="yes" required>
                                                <label class="form-check-label" for="donated_blood_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_donated_blood" id="donated_blood_no" value="no">
                                                <label class="form-check-label" for="donated_blood_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had any vaccinations or other shots?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_vaccine" id="had_vaccine_yes" value="yes" required>
                                                <label class="form-check-label" for="had_vaccine_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_vaccine" id="had_vaccine_no" value="no">
                                                <label class="form-check-label" for="had_vaccine_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had contact with someone who was vaccinated for smallpox in the past 8 weeks?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_contact_w_smallpox" id="contact_w_smallpox_yes" value="yes" required>
                                                <label class="form-check-label" for="contact_w_smallpox_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_contact_w_smallpox" id="contact_w_smallpox_no" value="no">
                                                <label class="form-check-label" for="contact_w_smallpox_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- IN THE PAST 3 MONTHS -->
                                <div class="mb-4">
                                    <h6 class="text-danger fw-bold mb-3">IN THE PAST 3 MONTHS, HAVE YOU</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Taken any medication by mouth (oral) to prevent an HIV infection?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_med_for_hiv" id="med_for_hiv_yes" value="yes" required>
                                                <label class="form-check-label" for="med_for_hiv_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_med_for_hiv" id="med_for_hiv_no" value="no">
                                                <label class="form-check-label" for="med_for_hiv_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had sexual contact with a new partner?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_new_partner" id="sex_new_partner_yes" value="yes" required>
                                                <label class="form-check-label" for="sex_new_partner_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_new_partner" id="sex_new_partner_no" value="no">
                                                <label class="form-check-label" for="sex_new_partner_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had sexual contact with more than one partner?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_more_partner" id="sex_more_partner_yes" value="yes" required>
                                                <label class="form-check-label" for="sex_more_partner_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_more_partner" id="sex_more_partner_no" value="no">
                                                <label class="form-check-label" for="sex_more_partner_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had sexual contact with anyone who has ever had a positive test for HIV infection?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_hiv_positive" id="sex_hiv_positive_yes" value="yes" required>
                                                <label class="form-check-label" for="sex_hiv_positive_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_hiv_positive" id="sex_hiv_positive_no" value="no">
                                                <label class="form-check-label" for="sex_hiv_positive_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Received money, drugs, or other payment for sex?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_get_payment" id="sex_get_payment_yes" value="yes" required>
                                                <label class="form-check-label" for="sex_get_payment_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_get_payment" id="sex_get_payment_no" value="no">
                                                <label class="form-check-label" for="sex_get_payment_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had sexual contact with anyone who has, in the past 3 months, received money, drugs or other payment for sex?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_partner_get_payment" id="sex_partner_get_payment_yes" value="yes" required>
                                                <label class="form-check-label" for="sex_partner_get_payment_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_partner_get_payment" id="sex_partner_get_payment_no" value="no">
                                                <label class="form-check-label" for="sex_partner_get_payment_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Used needles to inject drugs, steroids, or anything not prescribed by your doctor?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_used_injected_drugs" id="used_injected_drugs_yes" value="yes" required>
                                                <label class="form-check-label" for="used_injected_drugs_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_used_injected_drugs" id="used_injected_drugs_no" value="no">
                                                <label class="form-check-label" for="used_injected_drugs_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had sexual contact with anyone who has used needles in the past 3 months to inject drugs, steroids, or anything not prescribed by their doctor?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_partner_used_injected_drugs" id="sex_partner_used_injected_drugs_yes" value="yes" required>
                                                <label class="form-check-label" for="sex_partner_used_injected_drugs_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_partner_used_injected_drugs" id="sex_partner_used_injected_drugs_no" value="no">
                                                <label class="form-check-label" for="sex_partner_used_injected_drugs_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had syphilis or gonorrhea or been treated for syphilis or gonorrhea?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_syphilis_gonorrhea" id="had_syphilis_gonorrhea_yes" value="yes" required>
                                                <label class="form-check-label" for="had_syphilis_gonorrhea_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_syphilis_gonorrhea" id="had_syphilis_gonorrhea_no" value="no">
                                                <label class="form-check-label" for="had_syphilis_gonorrhea_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had sexual contact with a person who has hepatitis?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_partner_has_hepatitis" id="sex_partner_has_hepatitis_yes" value="yes" required>
                                                <label class="form-check-label" for="sex_partner_has_hepatitis_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_sex_partner_has_hepatitis" id="sex_partner_has_hepatitis_no" value="no">
                                                <label class="form-check-label" for="sex_partner_has_hepatitis_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Lived with a person who has hepatitis?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_live_with_hepatitis_patient" id="live_with_hepatitis_patient_yes" value="yes" required>
                                                <label class="form-check-label" for="live_with_hepatitis_patient_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_live_with_hepatitis_patient" id="live_with_hepatitis_patient_no" value="no">
                                                <label class="form-check-label" for="live_with_hepatitis_patient_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had an accidental needle-stick?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_accidental_needle_stick" id="accidental_needle_stick_yes" value="yes" required>
                                                <label class="form-check-label" for="accidental_needle_stick_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_accidental_needle_stick" id="accidental_needle_stick_no" value="no">
                                                <label class="form-check-label" for="accidental_needle_stick_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Come into contact with someone else's blood?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_contact_with_others_blood" id="contact_with_others_blood_yes" value="yes" required>
                                                <label class="form-check-label" for="contact_with_others_blood_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_contact_with_others_blood" id="contact_with_others_blood_no" value="no">
                                                <label class="form-check-label" for="contact_with_others_blood_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had a tattoo?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_tattoo" id="had_tattoo_yes" value="yes" required>
                                                <label class="form-check-label" for="had_tattoo_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_tattoo" id="had_tattoo_no" value="no">
                                                <label class="form-check-label" for="had_tattoo_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had ear or body piercing?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_piercing" id="had_piercing_yes" value="yes" required>
                                                <label class="form-check-label" for="had_piercing_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_piercing" id="had_piercing_no" value="no">
                                                <label class="form-check-label" for="had_piercing_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had a blood transfusion?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_transfusion" id="had_transfusion_yes" value="yes" required>
                                                <label class="form-check-label" for="had_transfusion_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_transfusion" id="had_transfusion_no" value="no">
                                                <label class="form-check-label" for="had_transfusion_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had a transplant such as organ, tissue, or bone marrow?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_transplant" id="had_transplant_yes" value="yes" required>
                                                <label class="form-check-label" for="had_transplant_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_transplant" id="had_transplant_no" value="no">
                                                <label class="form-check-label" for="had_transplant_no">No</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Had a graft such as bone or skin?</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_graft" id="had_graft_yes" value="yes" required>
                                                <label class="form-check-label" for="had_graft_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="screening_had_graft" id="had_graft_no" value="no">
                                                <label class="form-check-label" for="had_graft_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Note:</strong> Your input will be recorded and kept private in accordance with the Data Privacy Act of 2012. All information is reviewed by trained medical professionals.
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-heart me-2"></i>
                                Register as Donor
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('donation_date').min = today;
    
    // Form validation
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Registering...');
    });
});
</script>
@endpush

@push('styles')
<style>
/* Enhanced Cooldown Warning Styles */
.cooldown-warning-container {
    background: linear-gradient(135deg, #fff8e1 0%, #fff3e0 100%);
    border: 3px solid #ffc107;
    border-radius: 25px;
    padding: 3rem 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(255, 193, 7, 0.2);
}

.cooldown-warning-container::before {
    content: '';
    position: absolute;
    top: -100%;
    left: -100%;
    width: 300%;
    height: 300%;
    background: radial-gradient(circle, rgba(255, 193, 7, 0.1) 0%, transparent 60%);
    animation: warning-pulse 4s ease-in-out infinite;
}

@keyframes warning-pulse {
    0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.1; }
    50% { transform: scale(1.2) rotate(180deg); opacity: 0.2; }
}

.cooldown-header {
    position: relative;
    z-index: 2;
}

.cooldown-icon-container {
    position: relative;
    height: 120px;
}

.cooldown-icon-wrapper {
    position: relative;
    display: inline-block;
}

.cooldown-heart {
    font-size: 4rem;
    color: #dc3545;
    animation: heart-beat-warning 2s ease-in-out infinite;
    position: relative;
    z-index: 2;
}

@keyframes heart-beat-warning {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.15); }
    50% { transform: scale(1.3); }
    75% { transform: scale(1.15); }
}

.cooldown-clock-icon {
    font-size: 3rem;
    color: #ffc107;
    position: absolute;
    top: -20px;
    right: -30px;
    animation: clock-spin-warning 3s linear infinite;
    z-index: 1;
}

@keyframes clock-spin-warning {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.cooldown-content {
    position: relative;
    z-index: 2;
}

.countdown-display {
    margin-bottom: 2rem;
}

.countdown-circle {
    background: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
    border-radius: 50%;
    width: 150px;
    height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    box-shadow: 0 10px 30px rgba(255, 193, 7, 0.4);
    border: 4px solid #fff;
    position: relative;
    overflow: hidden;
}

.countdown-circle::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    animation: circle-shine 3s ease-in-out infinite;
}

@keyframes circle-shine {
    0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.3; }
    50% { transform: scale(1.1) rotate(180deg); opacity: 0.6; }
}

.countdown-number {
    font-size: 3.5rem;
    font-weight: bold;
    color: #fff;
    text-shadow: 0 3px 6px rgba(0,0,0,0.3);
    line-height: 1;
    margin-bottom: 0.25rem;
}

.countdown-unit {
    font-size: 1rem;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
}

.countdown-text {
    font-size: 1.1rem;
    color: #856404;
}

.info-card {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 193, 7, 0.3);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.info-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.info-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 1.1rem;
    color: #495057;
}

.date-highlight {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 2px solid #90caf9;
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
    font-size: 1.3rem;
    font-weight: bold;
    color: #1976d2;
    box-shadow: 0 4px 15px rgba(25, 118, 210, 0.2);
}

.cooldown-progress-section {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 193, 7, 0.3);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.progress {
    background-color: rgba(255, 193, 7, 0.2);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.progress-bar {
    background: linear-gradient(90deg, #ffc107 0%, #ff8f00 100%);
    border-radius: 10px;
    transition: width 1.5s ease-in-out;
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}

.cooldown-actions {
    margin-top: 2rem;
}

.cooldown-actions .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.cooldown-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Floating blood drop particles */
.cooldown-warning-container::after {
    content: '🩸';
    position: absolute;
    top: 30px;
    right: 40px;
    font-size: 2rem;
    animation: float-blood-drop 8s ease-in-out infinite;
    opacity: 0.7;
    z-index: 1;
}

@keyframes float-blood-drop {
    0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); opacity: 0.7; }
    25% { transform: translateY(-15px) rotate(90deg) scale(1.1); opacity: 1; }
    50% { transform: translateY(-30px) rotate(180deg) scale(1.2); opacity: 0.8; }
    75% { transform: translateY(-15px) rotate(270deg) scale(1.1); opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .cooldown-warning-container {
        padding: 2rem 1rem;
        border-radius: 20px;
    }
    
    .countdown-circle {
        width: 120px;
        height: 120px;
    }
    
    .countdown-number {
        font-size: 2.8rem;
    }
    
    .cooldown-heart {
        font-size: 3rem;
    }
    
    .cooldown-clock-icon {
        font-size: 2.5rem;
        top: -15px;
        right: -25px;
    }
}

/* Additional floating elements */
.cooldown-warning-container::before {
    content: '💉';
    position: absolute;
    bottom: 20px;
    left: 30px;
    font-size: 1.5rem;
    animation: float-syringe 10s ease-in-out infinite;
    opacity: 0.5;
    z-index: 1;
}

@keyframes float-syringe {
    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.5; }
    50% { transform: translateY(-25px) rotate(180deg); opacity: 0.8; }
}
</style>
@endpush
