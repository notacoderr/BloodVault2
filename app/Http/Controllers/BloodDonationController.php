<?php

namespace App\Http\Controllers;

use App\Models\BloodDonation;
use App\Models\BloodBank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BloodDonationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if user is verified (not deactivated).
     */
    private function checkUserVerification()
    {
        $user = Auth::user();
        if (!$user->is_verified) {
            Auth::logout();
            abort(403, 'Your account has been deactivated. Please contact an administrator.');
        }
    }

    /**
     * Show the blood donation form.
     */
    public function create()
    {
        $this->checkUserVerification();
        $user = Auth::user();
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        // Check if user can donate based on cooldown
        $canDonate = BloodDonation::canUserDonate($user->USER_ID);
        $nextEligibleDate = BloodDonation::getNextEligibleDate($user->USER_ID);
        $remainingDays = BloodDonation::getRemainingCooldownDays($user->USER_ID);
        
        return view('blood-donations.create', compact('bloodTypes', 'canDonate', 'nextEligibleDate', 'remainingDays'));
    }

    /**
     * Store a new blood donation request.
     */
    public function store(Request $request)
    {
        // Check if user can donate based on cooldown
        if (!BloodDonation::canUserDonate(Auth::id())) {
            $nextEligibleDate = BloodDonation::getNextEligibleDate(Auth::id());
            $remainingDays = BloodDonation::getRemainingCooldownDays(Auth::id());
            
            return back()->withErrors([
                'cooldown' => "You cannot donate blood yet. You must wait {$remainingDays} more days before your next donation. Your next eligible donation date is " . $nextEligibleDate->format('F j, Y') . "."
            ])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'blood_type' => 'required|string|max:10',
            'donation_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
            // Screening questions validation
            'screening_feels_healthy' => 'required|in:yes,no',
            'screening_taking_antibiotic' => 'required|in:yes,no',
            'screening_medfor_infection' => 'required|in:yes,no',
            'screening_currently_pregnant' => 'required|in:yes,no',
            'screening_took_aspirin' => 'required|in:yes,no',
            'screening_donated_blood' => 'required|in:yes,no',
            'screening_had_vaccine' => 'required|in:yes,no',
            'screening_contact_w_smallpox' => 'required|in:yes,no',
            'screening_med_for_hiv' => 'required|in:yes,no',
            'screening_sex_new_partner' => 'required|in:yes,no',
            'screening_sex_more_partner' => 'required|in:yes,no',
            'screening_sex_hiv_positive' => 'required|in:yes,no',
            'screening_sex_get_payment' => 'required|in:yes,no',
            'screening_sex_partner_get_payment' => 'required|in:yes,no',
            'screening_used_injected_drugs' => 'required|in:yes,no',
            'screening_sex_partner_used_injected_drugs' => 'required|in:yes,no',
            'screening_had_syphilis_gonorrhea' => 'required|in:yes,no',
            'screening_sex_partner_has_hepatitis' => 'required|in:yes,no',
            'screening_live_with_hepatitis_patient' => 'required|in:yes,no',
            'screening_accidental_needle_stick' => 'required|in:yes,no',
            'screening_contact_with_others_blood' => 'required|in:yes,no',
            'screening_had_tattoo' => 'required|in:yes,no',
            'screening_had_piercing' => 'required|in:yes,no',
            'screening_had_transfusion' => 'required|in:yes,no',
            'screening_had_transplant' => 'required|in:yes,no',
            'screening_had_graft' => 'required|in:yes,no',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Prepare screening answers
            $screening_answers = [
                'feels_healthy' => $request->screening_feels_healthy,
                'taking_antibiotic' => $request->screening_taking_antibiotic,
                'medfor_infection' => $request->screening_medfor_infection,
                'currently_pregnant' => $request->screening_currently_pregnant,
                'took_aspirin' => $request->screening_took_aspirin,
                'donated_blood' => $request->screening_donated_blood,
                'had_vaccine' => $request->screening_had_vaccine,
                'contact_w_smallpox' => $request->screening_contact_w_smallpox,
                'med_for_hiv' => $request->screening_med_for_hiv,
                'sex_new_partner' => $request->screening_sex_new_partner,
                'sex_more_partner' => $request->screening_sex_more_partner,
                'sex_hiv_positive' => $request->screening_sex_hiv_positive,
                'sex_get_payment' => $request->screening_sex_get_payment,
                'sex_partner_get_payment' => $request->screening_sex_partner_get_payment,
                'used_injected_drugs' => $request->screening_used_injected_drugs,
                'sex_partner_used_injected_drugs' => $request->screening_sex_partner_used_injected_drugs,
                'had_syphilis_gonorrhea' => $request->screening_had_syphilis_gonorrhea,
                'sex_partner_has_hepatitis' => $request->screening_sex_partner_has_hepatitis,
                'live_with_hepatitis_patient' => $request->screening_live_with_hepatitis_patient,
                'accidental_needle_stick' => $request->screening_accidental_needle_stick,
                'contact_with_others_blood' => $request->screening_contact_with_others_blood,
                'had_tattoo' => $request->screening_had_tattoo,
                'had_piercing' => $request->screening_had_piercing,
                'had_transfusion' => $request->screening_had_transfusion,
                'had_transplant' => $request->screening_had_transplant,
                'had_graft' => $request->screening_had_graft,
            ];

            $bloodDonation = BloodDonation::create([
                'user_id' => Auth::id(),
                'donor_name' => $request->donor_name,
                'donor_email' => $request->donor_email,
                'blood_type' => $request->blood_type,
                'donation_date' => $request->donation_date,
                'quantity' => 1, // Default to 1 unit
                'screening_status' => 'pending', // Default screening status
                'status' => 'pending',
                'screening_answers' => json_encode($screening_answers),
                'notes' => null, // Initialize as null
                'admin_notes' => null, // Initialize as null
            ]);

            return redirect('/user/dashboard')->with('status', 'Your blood donation request has been submitted and is awaiting admin approval.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to submit blood donation request. Please try again.'])->withInput();
        }
    }

    /**
     * Show blood donation details.
     */
    public function show($id)
    {
        $user = Auth::user();
        $bloodDonation = $user->bloodDonations()->findOrFail($id);
        
        return view('blood-donations.show', compact('bloodDonation'));
    }

    /**
     * Show the edit form for a blood donation.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $bloodDonation = $user->bloodDonations()->where('status', 'pending')->findOrFail($id);
        
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        return view('user.blood-donation-edit', compact('bloodDonation', 'bloodTypes'));
    }

    /**
     * Update a blood donation.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $bloodDonation = $user->bloodDonations()->where('status', 'pending')->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'blood_type' => 'required|string|max:10',
            'donation_date' => 'required|date|after:today',
            'screening_answers' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $bloodDonation->update($request->only([
                'donor_name', 'donor_email', 'blood_type', 'donation_date',
                'screening_answers', 'notes'
            ]));

            return redirect('/user/my-donations')->with('status', 'Blood donation updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update blood donation. Please try again.'])->withInput();
        }
    }

    /**
     * Cancel a blood donation.
     */
    public function cancel($id)
    {
        $user = Auth::user();
        
        // Debug logging
        \Log::info('Cancel donation attempt', [
            'user_id' => $user->id,
            'donation_id' => $id,
            'user_agent' => request()->userAgent()
        ]);
        
        try {
            $bloodDonation = $user->bloodDonations()->where('status', 'pending')->findOrFail($id);
            
            \Log::info('Donation found for cancellation', [
                'donation_id' => $bloodDonation->id,
                'current_status' => $bloodDonation->status
            ]);
            
            $bloodDonation->update(['status' => 'cancelled']);
            
            \Log::info('Donation cancelled successfully', [
                'donation_id' => $bloodDonation->id,
                'new_status' => $bloodDonation->status
            ]);
            
            return redirect('/user/my-donations')->with('status', 'Blood donation cancelled successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to cancel donation', [
                'user_id' => $user->id,
                'donation_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to cancel blood donation. Please try again.']);
        }
    }

    /**
     * Process a completed blood donation (admin function).
     */
    public function processDonation(Request $request, $id)
    {
        // This would typically be an admin function, but including it here for completeness
        $bloodDonation = BloodDonation::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:10',
            'expiration_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            // Update donation status to completed
            $bloodDonation->update(['status' => 'completed']);

            // Add to blood bank inventory
            BloodBank::create([
                'donor' => $bloodDonation->user_id,
                'blood_type' => $bloodDonation->blood_type,
                'acquisition_date' => now(),
                'expiration_date' => $request->expiration_date,
                'quantity' => $request->quantity,
                'status' => 1, // Approved
            ]);

            return back()->with('status', 'Blood donation processed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process blood donation. Please try again.']);
        }
    }

    /**
     * Get blood donation statistics for the authenticated user.
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_donations' => $user->bloodDonations()->count(),
            'pending_donations' => $user->bloodDonations()->pending()->count(),
            'approved_donations' => $user->bloodDonations()->approved()->count(),
            'completed_donations' => $user->bloodDonations()->completed()->count(),
            'rejected_donations' => $user->bloodDonations()->rejected()->count(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Show screening questions for blood donation.
     */
    public function showScreening()
    {
        $screeningQuestions = [
            'Have you had any illness in the past 48 hours?',
            'Are you currently taking any medications?',
            'Have you had any tattoos or piercings in the last 12 months?',
            'Have you traveled outside the country in the last 12 months?',
            'Have you had any surgery in the last 6 months?',
            'Are you pregnant or have you been pregnant in the last 6 weeks?',
            'Have you had any blood transfusions in the last 12 months?',
            'Do you have any chronic medical conditions?',
            'Have you had any exposure to HIV/AIDS or hepatitis?',
            'Are you feeling well today?'
        ];
        
        return view('blood-donations.screening', compact('screeningQuestions'));
    }

    /**
     * Submit screening answers.
     */
    public function submitScreening(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'blood_type' => 'required|string|max:10',
            'donation_date' => 'required|date|after:today',
            // Screening questions validation
            'screening_feels_healthy' => 'required|in:yes,no',
            'screening_taking_antibiotic' => 'required|in:yes,no',
            'screening_medfor_infection' => 'required|in:yes,no',
            'screening_currently_pregnant' => 'required|in:yes,no',
            'screening_took_aspirin' => 'required|in:yes,no',
            'screening_donated_blood' => 'required|in:yes,no',
            'screening_had_vaccine' => 'required|in:yes,no',
            'screening_contact_w_smallpox' => 'required|in:yes,no',
            'screening_med_for_hiv' => 'required|in:yes,no',
            'screening_sex_new_partner' => 'required|in:yes,no',
            'screening_sex_more_partner' => 'required|in:yes,no',
            'screening_sex_hiv_positive' => 'required|in:yes,no',
            'screening_sex_get_payment' => 'required|in:yes,no',
            'screening_sex_partner_get_payment' => 'required|in:yes,no',
            'screening_used_injected_drugs' => 'required|in:yes,no',
            'screening_sex_partner_used_injected_drugs' => 'required|in:yes,no',
            'screening_had_syphilis_gonorrhea' => 'required|in:yes,no',
            'screening_sex_partner_has_hepatitis' => 'required|in:yes,no',
            'screening_live_with_hepatitis_patient' => 'required|in:yes,no',
            'screening_accidental_needle_stick' => 'required|in:yes,no',
            'screening_contact_with_others_blood' => 'required|in:yes,no',
            'screening_had_tattoo' => 'required|in:yes,no',
            'screening_had_piercing' => 'required|in:yes,no',
            'screening_had_transfusion' => 'required|in:yes,no',
            'screening_had_transplant' => 'required|in:yes,no',
            'screening_had_graft' => 'required|in:yes,no',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Prepare screening answers
            $screening_answers = [
                'feels_healthy' => $request->screening_feels_healthy,
                'taking_antibiotic' => $request->screening_taking_antibiotic,
                'medfor_infection' => $request->screening_medfor_infection,
                'currently_pregnant' => $request->screening_currently_pregnant,
                'took_aspirin' => $request->screening_took_aspirin,
                'donated_blood' => $request->screening_donated_blood,
                'had_vaccine' => $request->screening_had_vaccine,
                'contact_w_smallpox' => $request->screening_contact_w_smallpox,
                'med_for_hiv' => $request->screening_med_for_hiv,
                'sex_new_partner' => $request->screening_sex_new_partner,
                'sex_more_partner' => $request->screening_sex_more_partner,
                'sex_hiv_positive' => $request->screening_sex_hiv_positive,
                'sex_get_payment' => $request->screening_sex_get_payment,
                'sex_partner_get_payment' => $request->screening_sex_partner_get_payment,
                'used_injected_drugs' => $request->screening_used_injected_drugs,
                'sex_partner_used_injected_drugs' => $request->screening_sex_partner_used_injected_drugs,
                'had_syphilis_gonorrhea' => $request->screening_had_syphilis_gonorrhea,
                'sex_partner_has_hepatitis' => $request->screening_sex_partner_has_hepatitis,
                'live_with_hepatitis_patient' => $request->screening_live_with_hepatitis_patient,
                'accidental_needle_stick' => $request->screening_accidental_needle_stick,
                'contact_with_others_blood' => $request->screening_contact_with_others_blood,
                'had_tattoo' => $request->screening_had_tattoo,
                'had_piercing' => $request->screening_had_piercing,
                'had_transfusion' => $request->screening_had_transfusion,
                'had_transplant' => $request->screening_had_transplant,
                'had_graft' => $request->screening_had_graft,
            ];

            $bloodDonation = BloodDonation::create([
                'user_id' => Auth::id(),
                'donor_name' => $request->donor_name,
                'donor_email' => $request->donor_email,
                'blood_type' => $request->blood_type,
                'donation_date' => $request->donation_date,
                'quantity' => 1, // Default to 1 unit
                'screening_status' => 'pending', // Default screening status
                'status' => 'pending',
                'screening_answers' => json_encode($screening_answers),
                'notes' => null, // Initialize as null
                'admin_notes' => null, // Initialize as null
            ]);

            return redirect('/user/dashboard')->with('status', 'Screening completed and donation request submitted! Your responses have been recorded and will be reviewed by medical professionals.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to submit screening. Please try again.'])->withInput();
        }
    }
}
