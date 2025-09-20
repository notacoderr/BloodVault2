<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BloodRequest;
use App\Models\BloodDonation;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
     * Show user dashboard.
     */
    public function dashboard()
    {
        $this->checkUserVerification();
        $user = Auth::user();
        
        // Get user's blood requests
        $bloodRequests = $user->bloodRequests()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get user's blood donations
        $bloodDonations = $user->bloodDonations()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get user's appointments
        $appointments = $user->appointments()
            ->orderBy('appointment_date', 'asc')
            ->take(5)
            ->get();
        
        // Get upcoming appointments
        $upcomingAppointments = $user->appointments()
            ->upcoming()
            ->take(3)
            ->get();
        
        // Get pending requests count
        $pendingRequestsCount = $user->bloodRequests()->pending()->count();
        
        // Get approved donations count
        $approvedDonationsCount = $user->bloodDonations()->approved()->count();
        
        return view('user.dashboard', compact(
            'user',
            'bloodRequests',
            'bloodDonations',
            'appointments',
            'upcomingAppointments',
            'pendingRequestsCount',
            'approvedDonationsCount'
        ));
    }

    /**
     * Show user profile.
     */
    public function profile()
    {
        $this->checkUserVerification();
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $this->checkUserVerification();
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'dob' => 'nullable|string|max:10',
            'sex' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'contact' => 'nullable|string|max:11',
            'bloodtype' => 'nullable|string|max:4',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user->update($request->only([
                'name', 'dob', 'sex', 'address', 'city', 
                'province', 'contact', 'bloodtype'
            ]));

            return back()->with('status', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update profile. Please try again.'])->withInput();
        }
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request)
    {
        $this->checkUserVerification();
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        // Check current password
        if ($user->password !== $request->current_password) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        try {
            $user->update(['password' => $request->new_password]);
            return back()->with('status', 'Password changed successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to change password. Please try again.'])->withInput();
        }
    }

    /**
     * Show user's blood requests.
     */
    public function myRequests()
    {
        $this->checkUserVerification();
        $user = Auth::user();
        $bloodRequests = $user->bloodRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('user.my-requests', compact('bloodRequests'));
    }

    /**
     * Show user's blood donations.
     */
    public function myDonations()
    {
        $this->checkUserVerification();
        $user = Auth::user();
        $bloodDonations = $user->bloodDonations()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('user.my-donations', compact('bloodDonations'));
    }

    /**
     * Get dashboard status counts for the current user.
     */
    public function getDashboardStatusCounts()
    {
        try {
            $userId = auth()->id();
            
            $counts = [
                'pending_requests' => BloodRequest::where('user_id', $userId)
                    ->where('status', 'pending')
                    ->count(),
                'pending_donations' => BloodDonation::where('user_id', $userId)
                    ->where('status', 'pending')
                    ->count(),
                'pending_appointments' => Appointment::where('user_id', $userId)
                    ->where('status', 'pending')
                    ->count(),
                'total_requests' => BloodRequest::where('user_id', $userId)->count(),
                'total_donations' => BloodDonation::where('user_id', $userId)->count(),
                'total_appointments' => Appointment::where('user_id', $userId)->count(),
            ];
            
            return response()->json($counts);
        } catch (\Exception $e) {
            \Log::error('Failed to get dashboard status counts: ' . $e->getMessage());
            return response()->json([
                'pending_requests' => 0,
                'pending_donations' => 0,
                'pending_appointments' => 0,
                'total_requests' => 0,
                'total_donations' => 0,
                'total_appointments' => 0,
            ], 500);
        }
    }

    /**
     * Get donation cooldown information for the current user.
     */
    public function getDonationCooldown()
    {
        try {
            $userId = auth()->id();
            
            $canDonate = BloodDonation::canUserDonate($userId);
            $nextEligibleDate = BloodDonation::getNextEligibleDate($userId);
            $remainingDays = BloodDonation::getRemainingCooldownDays($userId);
            
            $data = [
                'can_donate' => $canDonate,
                'remaining_days' => $remainingDays,
                'next_eligible_date' => $nextEligibleDate ? $nextEligibleDate->format('F j, Y') : null,
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Failed to get donation cooldown info: ' . $e->getMessage());
            return response()->json([
                'can_donate' => true,
                'remaining_days' => 0,
                'next_eligible_date' => null,
            ], 500);
        }
    }

    /**
     * Show user's appointments.
     */
    public function myAppointments()
    {
        $this->checkUserVerification();
        $user = Auth::user();
        $appointments = $user->appointments()
            ->orderBy('appointment_date', 'asc')
            ->paginate(10);
        
        return view('user.my-appointments', compact('appointments'));
    }

    /**
     * Show request details.
     */
    public function requestDetails($id)
    {
        $user = Auth::user();
        $bloodRequest = $user->bloodRequests()->findOrFail($id);
        
        return view('user.request-details', compact('bloodRequest'));
    }

    /**
     * Show donation details.
     */
    public function donationDetails($id)
    {
        $user = Auth::user();
        $bloodDonation = $user->bloodDonations()->findOrFail($id);
        
        return view('user.donation-details', compact('bloodDonation'));
    }

    /**
     * Show appointment details.
     */
    public function appointmentDetails($id)
    {
        $user = Auth::user();
        $appointment = $user->appointments()->findOrFail($id);
        
        return view('user.appointment-details', compact('appointment'));
    }

    /**
     * Cancel a blood request.
     */
    public function cancelRequest($id)
    {
        $user = Auth::user();
        $bloodRequest = $user->bloodRequests()->findOrFail($id);
        
        if ($bloodRequest->status === 'pending') {
            $bloodRequest->update(['status' => 'cancelled']);
            return back()->with('status', 'Request cancelled successfully!');
        }
        
        return back()->withErrors(['error' => 'Cannot cancel this request.']);
    }

    /**
     * Cancel an appointment.
     */
    public function cancelAppointment($id)
    {
        $user = Auth::user();
        $appointment = $user->appointments()->findOrFail($id);
        
        if ($appointment->status === 'pending' || $appointment->status === 'confirmed') {
            $appointment->update(['status' => 'cancelled']);
            return back()->with('status', 'Appointment cancelled successfully!');
        }
        
        return back()->withErrors(['error' => 'Cannot cancel this appointment.']);
    }

    /**
     * Get dashboard statistics for AJAX requests.
     */
    public function getDashboardStats()
    {
        $user = Auth::user();
        
        $stats = [
            'pending_requests' => $user->bloodRequests()->pending()->count(),
            'approved_donations' => $user->bloodDonations()->approved()->count(),
            'upcoming_appointments' => $user->appointments()->upcoming()->count(),
            'total_requests' => $user->bloodRequests()->count(),
            'total_donations' => $user->bloodDonations()->count(),
            'total_appointments' => $user->appointments()->count(),
        ];
        
        return response()->json($stats);
    }
}
