@extends('layouts.app')

@section('title', 'Book Appointment - Life Vault')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-check me-2"></i>
                        Book Appointment
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

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Schedule your appointment!</strong> Choose a convenient time for your blood donation or health screening.
                    </div>

                    <form action="{{ route('appointment.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="appointment_type" class="form-label">Appointment Type</label>
                                <select class="form-select" id="appointment_type" name="appointment_type" required>
                                    <option value="">Select Type</option>
                                    <option value="donation" {{ old('appointment_type') == 'donation' ? 'selected' : '' }}>Blood Donation</option>
                                    <option value="screening" {{ old('appointment_type') == 'screening' ? 'selected' : '' }}>Health Screening</option>
                                    <option value="consultation" {{ old('appointment_type') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="appointment_date" class="form-label">Preferred Date</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="time_slot" class="form-label">Preferred Time</label>
                                <select class="form-select" id="time_slot" name="time_slot" required>
                                    <option value="">Select Time</option>
                                    <option value="09:00" {{ old('time_slot') == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                    <option value="10:00" {{ old('time_slot') == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                    <option value="11:00" {{ old('time_slot') == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                    <option value="13:00" {{ old('time_slot') == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                    <option value="14:00" {{ old('time_slot') == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                    <option value="15:00" {{ old('time_slot') == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                    <option value="16:00" {{ old('time_slot') == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Blood Type (if applicable)</label>
                                <select class="form-select" id="blood_type" name="blood_type">
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
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any special requirements, medical conditions, or additional information...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Important Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>Please arrive 15 minutes before your scheduled appointment</li>
                                    <li>Bring a valid ID and any relevant medical documents</li>
                                    <li>For blood donation: Eat a light meal 2-3 hours before</li>
                                    <li>Stay hydrated and avoid alcohol 24 hours before</li>
                                    <li>Wear comfortable clothing with loose sleeves</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-calendar-check me-2"></i>
                                Book Appointment
                            </button>
                        </div>
                    </form>
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
    document.getElementById('appointment_date').min = today;
    
    // Form validation
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Booking...');
    });
    
    // Show/hide blood type field based on appointment type
    $('#appointment_type').on('change', function() {
        const bloodTypeField = $('#blood_type').closest('.mb-3');
        if ($(this).val() === 'donation') {
            bloodTypeField.show();
        } else {
            bloodTypeField.hide();
        }
    });
});
</script>
@endpush
