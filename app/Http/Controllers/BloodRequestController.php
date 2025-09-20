<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\BloodBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BloodRequestController extends Controller
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
     * Show the blood request form.
     */
    public function create()
    {
        $this->checkUserVerification();
        $user = Auth::user();
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $urgencyLevels = ['low', 'medium', 'high', 'critical'];
        
        return view('blood-requests.create', compact('bloodTypes', 'urgencyLevels'));
    }

    /**
     * Store a new blood request.
     */
    public function store(Request $request)
    {
        $this->checkUserVerification();
        $validator = Validator::make($request->all(), [
            'blood_type' => 'required|string|max:5',
            'units_needed' => 'required|integer|min:1|max:100',
            'urgency' => 'required|in:low,medium,high,critical',
            'reason' => 'nullable|string',
            'hospital' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'request_date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $bloodRequest = BloodRequest::create([
                'user_id' => Auth::id(),
                'blood_type' => $request->blood_type,
                'units_needed' => $request->units_needed,
                'urgency' => $request->urgency,
                'reason' => $request->reason,
                'hospital' => $request->hospital,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'request_date' => $request->request_date,
                'status' => 'pending',
            ]);

            // Check blood availability
            $availableBlood = BloodBank::available()
                ->where('blood_type', $request->blood_type)
                ->sum('quantity');

            if ($availableBlood >= $request->units_needed) {
                $bloodRequest->update(['blood_available' => true]);
            }

            return redirect('/user/dashboard')->with('status', 'Your blood request has been submitted and is awaiting admin approval.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to submit blood request. Please try again.'])->withInput();
        }
    }

    /**
     * Show blood request details.
     */
    public function show($id)
    {
        $user = Auth::user();
        $bloodRequest = $user->bloodRequests()->findOrFail($id);
        
        return view('blood-requests.show', compact('bloodRequest'));
    }

    /**
     * Show the edit form for a blood request.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $bloodRequest = $user->bloodRequests()->where('status', 'pending')->findOrFail($id);
        
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $urgencyLevels = ['low', 'medium', 'high', 'critical'];
        
        return view('user.blood-request-edit', compact('bloodRequest', 'bloodTypes', 'urgencyLevels'));
    }

    /**
     * Update a blood request.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $bloodRequest = $user->bloodRequests()->where('status', 'pending')->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'blood_type' => 'required|string|max:5',
            'units_needed' => 'required|integer|min:1|max:100',
            'urgency' => 'required|in:low,medium,high,critical',
            'reason' => 'nullable|string',
            'hospital' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'request_date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $bloodRequest->update($request->only([
                'blood_type', 'units_needed', 'urgency', 'reason',
                'hospital', 'contact_person', 'contact_number', 'request_date'
            ]));

            return redirect('/user/my-requests')->with('status', 'Blood request updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update blood request. Please try again.'])->withInput();
        }
    }

    /**
     * Cancel a blood request.
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $bloodRequest = $user->bloodRequests()->where('status', 'pending')->findOrFail($id);
        
        try {
            $bloodRequest->update(['status' => 'cancelled']);
            return redirect('/user/my-requests')->with('status', 'Blood request cancelled successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to cancel blood request. Please try again.']);
        }
    }

    /**
     * Check blood availability for a specific type.
     */
    public function checkAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'blood_type' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid blood type'], 400);
        }

        $availableBlood = BloodBank::available()
            ->where('blood_type', $request->blood_type)
            ->sum('quantity');

        $expiringSoon = BloodBank::available()
            ->where('blood_type', $request->blood_type)
            ->where('expiration_date', '<=', now()->addDays(30))
            ->sum('quantity');

        return response()->json([
            'blood_type' => $request->blood_type,
            'available_units' => $availableBlood,
            'expiring_soon' => $expiringSoon,
            'status' => $availableBlood > 0 ? 'available' : 'unavailable'
        ]);
    }

    /**
     * Get blood request statistics for the authenticated user.
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_requests' => $user->bloodRequests()->count(),
            'pending_requests' => $user->bloodRequests()->pending()->count(),
            'approved_requests' => $user->bloodRequests()->approved()->count(),
            'completed_requests' => $user->bloodRequests()->where('status', 'completed')->count(),
            'cancelled_requests' => $user->bloodRequests()->where('status', 'cancelled')->count(),
        ];
        
        return response()->json($stats);
    }
}
