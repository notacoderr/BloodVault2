<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BloodRequest;
use App\Models\BloodDonation;
use App\Models\Appointment;
use App\Models\BloodBank;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show admin dashboard.
     */
    public function dashboard()
    {
        // Get dashboard statistics
        $totalUsers = User::count();
        $totalRequests = BloodRequest::count();
        $totalDonations = BloodDonation::count();
        $totalAppointments = Appointment::count();
        
        // Get pending items
        $pendingRequests = BloodRequest::pending()->count();
        $pendingDonations = BloodDonation::pending()->count();
        $pendingAppointments = Appointment::pending()->count();
        
        // Get critical requests
        $criticalRequests = BloodRequest::critical()->count();
        $highRequests = BloodRequest::high()->count();
        
        // Get recent requests
        $recentRequests = BloodRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get recent donations
        $recentDonations = BloodDonation::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get today's appointments
        $todayAppointments = Appointment::today()->with('user')->get();
        
        // Get blood inventory stats
        $availableBlood = BloodBank::available()->sum('quantity');
        $expiredBlood = BloodBank::expired()->sum('quantity');
        
        // Get low stock alerts
        $lowStockAlerts = [];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        foreach ($bloodTypes as $type) {
            $available = BloodBank::available()->where('blood_type', $type)->sum('quantity');
            if ($available <= 5) {
                $lowStockAlerts[$type] = $available;
            }
        }
        
        // Get expiring soon
        $expiringSoon = BloodBank::available()
            ->where('expiration_date', '<=', now()->addDays(30))
            ->where('expiration_date', '>', now())
            ->orderBy('expiration_date', 'asc')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalRequests',
            'totalDonations',
            'totalAppointments',
            'pendingRequests',
            'pendingDonations',
            'pendingAppointments',
            'criticalRequests',
            'highRequests',
            'recentRequests',
            'recentDonations',
            'todayAppointments',
            'availableBlood',
            'expiredBlood',
            'lowStockAlerts',
            'expiringSoon'
        ));
    }

    /**
     * Show all users.
     */
    public function users(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        
        // If it's an AJAX request, return JSON data
        if ($request->ajax()) {
            return response()->json([
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'prev_page_url' => $users->previousPageUrl(),
                    'next_page_url' => $users->nextPageUrl(),
                ]
            ]);
        }
        
        return view('admin.users', compact('users'));
    }

    /**
     * Show user details.
     */
    public function userDetails(User $user)
    {
        $user = $user->load(['bloodRequests', 'bloodDonations', 'appointments']);
        
        // If it's an AJAX request, return JSON
        if (request()->ajax()) {
            return response()->json($user);
        }
        
        return view('admin.user-details', compact('user'));
    }

    /**
     * Show user edit form.
     */
    public function editUser(User $user)
    {
        return view('admin.user-edit', compact('user'));
    }

    /**
     * Update user details.
     */
    public function updateUser(Request $request, User $user)
    {
        // Debug: Log the request data
        \Log::info('User update request', [
            'user_id' => $user->USER_ID,
            'request_data' => $request->all(),
            'user_before' => $user->toArray()
        ]);
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->USER_ID . ',USER_ID',
            'usertype' => 'required|in:admin,donor,requester',
            'bloodtype' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'dob' => 'nullable|date|before:today',
            'sex' => 'nullable|in:male,female,other',
            'contact' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'is_verified' => 'boolean',
        ]);

        if ($validator->fails()) {
            // Debug: Log validation errors
            \Log::error('User update validation failed', [
                'user_id' => $user->USER_ID,
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Prepare the data to update
            $updateData = $request->only([
                'name', 'email', 'usertype', 'bloodtype', 'dob', 'sex', 
                'contact', 'address', 'city', 'province'
            ]);
            
            // Handle the checkbox for is_verified
            $updateData['is_verified'] = $request->has('is_verified') ? 1 : 0;
            
            // Update user data
            $user->update($updateData);
            
            // Debug: Log the updated user data
            \Log::info('User updated successfully', [
                'user_id' => $user->USER_ID,
                'update_data' => $updateData,
                'user_after' => $user->fresh()->toArray()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully!',
                    'user' => $user->fresh()
                ]);
            }
            
            return redirect()->route('admin.user-details', $user->USER_ID)
                ->with('status', 'User updated successfully!');
                
        } catch (\Exception $e) {
            // Debug: Log the exception
            \Log::error('User update failed', [
                'user_id' => $user->USER_ID,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update user. Please try again.'])->withInput();
        }
    }

    /**
     * Update user status (toggle verification).
     */
    public function updateUserStatus(Request $request, User $user)
    {
        try {
            // Toggle the verification status
            $newStatus = !$user->is_verified;
            $user->update(['is_verified' => $newStatus]);
            
            $statusText = $newStatus ? 'activated' : 'deactivated';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "User account {$statusText} successfully!",
                    'new_status' => $newStatus
                ]);
            }
            
            return back()->with('status', "User account {$statusText} successfully!");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user status.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update user status.']);
        }
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $user)
    {
        try {
            $user->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User deleted successfully!'
                ]);
            }
            
            return redirect('/admin/users')->with('status', 'User deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete user.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to delete user.']);
        }
    }

    /**
     * Show all blood requests.
     */
    public function bloodRequests(Request $request)
    {
        $query = BloodRequest::with('user');
        
        // Apply filters
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('urgency') && $request->urgency !== '') {
            $query->where('urgency', $request->urgency);
        }
        
        if ($request->filled('blood_type') && $request->blood_type !== '') {
            $query->where('blood_type', $request->blood_type);
        }
        
        // Apply search
        if ($request->filled('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('blood_type', 'LIKE', "%{$search}%")
                  ->orWhere('urgency', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        $requests = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // If it's an AJAX request with limit parameter (dashboard request), return limited data
        if ($request->ajax() && $request->has('limit')) {
            $limitedRequests = BloodRequest::with('user')
                ->orderBy('created_at', 'desc')
                ->take($request->limit)
                ->get();
            
            return response()->json([
                'requests' => $limitedRequests
            ]);
        }
        
        // If it's a regular AJAX request (admin page), return paginated data
        if ($request->ajax()) {
            return response()->json([
                'requests' => $requests->items(),
                'pagination' => [
                    'current_page' => $requests->currentPage(),
                    'last_page' => $requests->lastPage(),
                    'per_page' => $requests->perPage(),
                    'total' => $requests->total(),
                    'prev_page_url' => $requests->previousPageUrl(),
                    'next_page_url' => $requests->nextPageUrl(),
                ]
            ]);
        }
        
        return view('admin.blood-requests', compact('requests'));
    }

    /**
     * Show blood request details.
     */
    public function bloodRequestDetails($id)
    {
        $request = BloodRequest::with('user')->findOrFail($id);
        
        // If it's an AJAX request, return JSON
        if (request()->ajax()) {
            return response()->json($request);
        }
        
        return view('admin.blood-request-details', compact('request'));
    }

    /**
     * Show blood donation details.
     */
    public function bloodDonationDetails($id)
    {
        try {
        $donation = BloodDonation::with('user')->findOrFail($id);
        
        // If it's an AJAX request, return JSON
        if (request()->ajax()) {
            return response()->json($donation);
        }
        
        return view('admin.blood-donation-details', compact('donation'));
        } catch (\Exception $e) {
            \Log::error('Failed to find blood donation: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Blood donation not found.'
                ], 404);
            }
            
            abort(404, 'Blood donation not found.');
        }
    }

    /**
     * Show the edit form for a blood request.
     */
    public function editBloodRequest($id)
    {
        $bloodRequest = BloodRequest::with('user')->findOrFail($id);
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $urgencyLevels = ['low', 'medium', 'high', 'critical'];
        
        return view('blood-requests.edit', compact('bloodRequest', 'bloodTypes', 'urgencyLevels'));
    }

    /**
     * Update a blood request.
     */
    public function updateBloodRequest(Request $request, $id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'blood_type' => 'required|string|max:5',
            'units_needed' => 'required|integer|min:1|max:50',
            'urgency' => 'required|in:low,medium,high,critical',
            'reason' => 'required|string',
            'hospital' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'status' => 'required|in:pending,approved,rejected,completed,cancelled',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $bloodRequest->update($request->only([
                'blood_type', 'units_needed', 'urgency', 'reason',
                'hospital', 'contact_person', 'contact_number', 'status', 'admin_notes'
            ]));

            return redirect()->route('admin.blood-requests')->with('status', 'Blood request updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update blood request. Please try again.'])->withInput();
        }
    }

    /**
     * Update blood request status.
     */
    public function updateRequestStatus(Request $request, $id)
    {
        // Debug: Log the request data
        \Log::info('Blood request status update attempt', [
            'request_id' => $id,
            'request_data' => $request->all(),
            'raw_input' => $request->getContent(),
            'user_agent' => $request->userAgent(),
            'database_connection' => \DB::connection()->getDatabaseName()
        ]);
        
        try {
            $bloodRequest = BloodRequest::findOrFail($id);
            \Log::info('Blood request found', [
                'request_id' => $bloodRequest->id,
                'current_status' => $bloodRequest->status,
                'allocated_units' => $bloodRequest->allocated_units,
                'fillable_attributes' => $bloodRequest->getFillable()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to find blood request', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
        // Clean the request data before validation
        $cleanData = $request->all();
        if (isset($cleanData['allocated_units']) && $cleanData['allocated_units'] === '') {
            $cleanData['allocated_units'] = null;
        }
        
        \Log::info('Cleaned data for validation', [
            'request_id' => $id,
            'original_data' => $request->all(),
            'cleaned_data' => $cleanData,
            'auth_user' => auth()->user() ? auth()->user()->USER_ID : 'not_authenticated',
            'session_id' => session()->getId()
        ]);
        
        $validator = Validator::make($cleanData, [
            'status' => 'required|in:pending,approved,rejected,completed,cancelled',
            'admin_notes' => 'nullable|string',
            'allocated_units' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            \Log::error('Blood request validation failed', [
                'request_id' => $id,
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            \Log::info('Attempting to update blood request', [
                'request_id' => $bloodRequest->id,
                'update_data' => $request->only(['status', 'admin_notes', 'allocated_units'])
            ]);
            
            // Check if the update data is valid
            $updateData = $request->only(['status', 'admin_notes', 'allocated_units']);
            
            // Clean up the data - convert empty strings to null for allocated_units
            if (isset($updateData['allocated_units']) && $updateData['allocated_units'] === '') {
                $updateData['allocated_units'] = null;
            }
            
            // Ensure admin_notes is never null
            if (isset($updateData['admin_notes']) && $updateData['admin_notes'] === null) {
                $updateData['admin_notes'] = '';
            }
            
            \Log::info('Update data prepared', ['update_data' => $updateData]);
            
            // Log the current state of the blood request
            \Log::info('Current blood request state', [
                'request_id' => $bloodRequest->id,
                'current_status' => $bloodRequest->status,
                'current_allocated_units' => $bloodRequest->allocated_units,
                'current_admin_notes' => $bloodRequest->admin_notes
            ]);
            
            // If status is being changed to completed, validate inventory availability first
            if ($request->status === 'completed' && $bloodRequest->status !== 'completed') {
                try {
                    $unitsToReduce = $request->allocated_units ?? $bloodRequest->units_needed;
                    
                    if ($unitsToReduce && $unitsToReduce > 0) {
                        // Check if sufficient blood inventory is available
                        $availableInventory = BloodBank::where('blood_type', $bloodRequest->blood_type)
                            ->where('status', 1) // Available
                            ->where('quantity', '>', 0)
                            ->where('expiration_date', '>', now()) // Not expired
                            ->sum('quantity');
                        
                        if ($availableInventory < $unitsToReduce) {
                            $errorMessage = "Insufficient blood inventory. Required: {$unitsToReduce} units, Available: {$availableInventory} units.";
                            \Log::warning($errorMessage);
                            
                            if ($request->ajax()) {
                                return response()->json([
                                    'success' => false,
                                    'message' => $errorMessage
                                ], 422);
                            }
                            
                            return back()->withErrors(['error' => $errorMessage]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to validate inventory availability: ' . $e->getMessage());
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to validate inventory availability: ' . $e->getMessage()
                        ], 500);
                    }
                    
                    return back()->withErrors(['error' => 'Failed to validate inventory availability: ' . $e->getMessage()]);
                }
            }
            
            // Perform the update
            try {
                $updated = $bloodRequest->update($updateData);
                \Log::info('Update result', ['updated' => $updated, 'request_id' => $bloodRequest->id]);
                
                if (!$updated) {
                    \Log::error('Blood request update returned false', [
                        'request_id' => $bloodRequest->id,
                        'update_data' => $updateData
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Exception during blood request update', [
                    'request_id' => $bloodRequest->id,
                    'update_data' => $updateData,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
            if ($updated) {
                // Refresh the model to get the updated data
                $bloodRequest->refresh();
                
                \Log::info('Blood request updated successfully', [
                    'request_id' => $bloodRequest->id,
                    'old_status' => $bloodRequest->getOriginal('status'),
                    'new_status' => $bloodRequest->status,
                    'allocated_units' => $bloodRequest->allocated_units,
                    'update_data' => $updateData
                ]);
                
                // If status is changed to completed, reduce blood inventory
                if ($request->status === 'completed' && $bloodRequest->getOriginal('status') !== 'completed') {
                    \Log::info('Attempting to reduce blood inventory for newly completed request', [
                        'request_id' => $bloodRequest->id,
                        'old_status' => $bloodRequest->status,
                        'new_status' => $request->status,
                        'allocated_units' => $request->allocated_units,
                        'units_needed' => $bloodRequest->units_needed
                    ]);
                    
                    try {
                        // Create a temporary object with updated data for inventory reduction
                        $tempRequest = clone $bloodRequest;
                        $tempRequest->allocated_units = $request->allocated_units;
                        
                        $this->reduceBloodInventory($tempRequest);
                        \Log::info('Blood inventory reduced successfully for newly completed request: ' . $bloodRequest->id);
                    } catch (\Exception $e) {
                        \Log::error('Failed to reduce blood inventory: ' . $e->getMessage());
                        // Rollback the status update if inventory reduction fails
                        $bloodRequest->update(['status' => $bloodRequest->status]);
                        
                        if ($request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Failed to complete request: ' . $e->getMessage()
                            ], 500);
                        }
                        
                        return back()->withErrors(['error' => 'Failed to complete request: ' . $e->getMessage()]);
                    }
                } 
                // If request is already completed but allocated_units is being updated, handle inventory adjustment
                elseif ($request->status === 'completed' && $bloodRequest->getOriginal('status') === 'completed' && 
                        $request->allocated_units && $request->allocated_units > 0 && 
                        $bloodRequest->getOriginal('allocated_units') !== $request->allocated_units) {
                    
                    \Log::info('Attempting to adjust blood inventory for already completed request', [
                        'request_id' => $bloodRequest->id,
                        'old_allocated_units' => $bloodRequest->getOriginal('allocated_units'),
                        'new_allocated_units' => $request->allocated_units,
                        'status' => $request->status
                    ]);
                    
                    try {
                        // Create a temporary object with updated data for inventory reduction
                        $tempRequest = clone $bloodRequest;
                        $tempRequest->allocated_units = $request->allocated_units;
                        
                        $this->reduceBloodInventory($tempRequest);
                        \Log::info('Blood inventory adjusted successfully for already completed request: ' . $bloodRequest->id);
                    } catch (\Exception $e) {
                        \Log::error('Failed to adjust blood inventory: ' . $e->getMessage());
                        
                        if ($request->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Failed to adjust inventory: ' . $e->getMessage()
                            ], 500);
                        }
                        
                        return back()->withErrors(['error' => 'Failed to adjust inventory: ' . $e->getMessage()]);
                    }
                } else {
                    \Log::info('Skipping inventory reduction', [
                        'request_id' => $bloodRequest->id,
                        'requested_status' => $request->status,
                        'current_status' => $bloodRequest->status,
                        'status_changing_to_completed' => $request->status === 'completed',
                        'was_already_completed' => $bloodRequest->status === 'completed',
                        'allocated_units_changing' => $request->allocated_units !== $bloodRequest->getOriginal('allocated_units')
                    ]);
                }
            } else {
                \Log::error('Blood request update failed', [
                    'request_id' => $bloodRequest->id,
                    'update_data' => $updateData
                ]);
            }
            
            // Send notification to user about status change
            try {
                $this->notifyUserOfStatusChange($bloodRequest, $request->status, $request->admin_notes);
                \Log::info('Notification sent successfully');
            } catch (\Exception $e) {
                \Log::error('Failed to send notification', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the entire request if notification fails
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Request status updated successfully!',
                    'request' => $bloodRequest->fresh()
                ]);
            }
            
            return back()->with('status', 'Request status updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update request status.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update request status.']);
        }
    }

    /**
     * Check if a blood request can be completed based on inventory availability.
     */
    public function checkRequestCompletionEligibility($id)
    {
        try {
            $bloodRequest = BloodRequest::findOrFail($id);
            
            if ($bloodRequest->status === 'completed') {
                return response()->json([
                    'can_complete' => false,
                    'message' => 'Request is already completed.',
                    'reason' => 'already_completed'
                ]);
            }
            
            $unitsNeeded = $bloodRequest->allocated_units ?? $bloodRequest->units_needed;
            
            if (!$unitsNeeded || $unitsNeeded <= 0) {
                return response()->json([
                    'can_complete' => false,
                    'message' => 'No units specified for completion.',
                    'reason' => 'no_units_specified'
                ]);
            }
            
            // Check available inventory
            $availableInventory = BloodBank::where('blood_type', $bloodRequest->blood_type)
                ->where('status', 1) // Available
                ->where('quantity', '>', 0)
                ->where('expiration_date', '>', now()) // Not expired
                ->sum('quantity');
            
            $canComplete = $availableInventory >= $unitsNeeded;
            
            return response()->json([
                'can_complete' => $canComplete,
                'units_needed' => $unitsNeeded,
                'available_inventory' => $availableInventory,
                'message' => $canComplete 
                    ? "Request can be completed. {$unitsNeeded} units available."
                    : "Insufficient inventory. Required: {$unitsNeeded}, Available: {$availableInventory}",
                'reason' => $canComplete ? 'sufficient_inventory' : 'insufficient_inventory'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to check request completion eligibility: ' . $e->getMessage());
            
            return response()->json([
                'can_complete' => false,
                'message' => 'Failed to check eligibility: ' . $e->getMessage(),
                'reason' => 'error'
            ], 500);
        }
    }

    /**
     * Test method for inventory reduction (temporary for debugging).
     */
    public function testInventoryReduction($id)
    {
        try {
            $bloodRequest = BloodRequest::findOrFail($id);
            
            // Test basic BloodBank queries
            $totalInventory = BloodBank::count();
            $availableInventory = BloodBank::where('status', 1)->count();
            $matchingBloodType = BloodBank::where('blood_type', $bloodRequest->blood_type)->count();
            
            \Log::info("BloodBank query test results", [
                'total_inventory' => $totalInventory,
                'available_inventory' => $availableInventory,
                'matching_blood_type' => $matchingBloodType,
                'request_blood_type' => $bloodRequest->blood_type
            ]);
            
            // Create a temporary object with test data
            $tempRequest = clone $bloodRequest;
            $tempRequest->allocated_units = 2; // Test with 2 units
            
            \Log::info("Testing inventory reduction for request: {$id}");
            
            $this->reduceBloodInventory($tempRequest);
            
            return response()->json([
                'success' => true,
                'message' => 'Test inventory reduction completed successfully!',
                'request_id' => $id,
                'test_results' => [
                    'total_inventory' => $totalInventory,
                    'available_inventory' => $availableInventory,
                    'matching_blood_type' => $matchingBloodType
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Test inventory reduction failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simple test method to verify basic functionality.
     */
    public function testSimple($id)
    {
        try {
            // Test 1: Check if we can find the blood request
            $bloodRequest = BloodRequest::find($id);
            
            if (!$bloodRequest) {
                return response()->json([
                    'success' => false,
                    'message' => "Blood request with ID {$id} not found",
                    'available_ids' => BloodRequest::pluck('id')->take(10)->toArray()
                ]);
            }
            
            // Test 2: Check basic BloodBank queries
            $totalInventory = BloodBank::count();
            $availableInventory = BloodBank::where('status', 1)->count();
            $matchingBloodType = BloodBank::where('blood_type', $bloodRequest->blood_type)->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Basic tests passed!',
                'blood_request' => [
                    'id' => $bloodRequest->id,
                    'blood_type' => $bloodRequest->blood_type,
                    'status' => $bloodRequest->status,
                    'units_needed' => $bloodRequest->units_needed,
                    'allocated_units' => $bloodRequest->allocated_units
                ],
                'inventory' => [
                    'total' => $totalInventory,
                    'available' => $availableInventory,
                    'matching_blood_type' => $matchingBloodType
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Show all blood donations.
     */
    public function bloodDonations(Request $request)
    {
        $query = BloodDonation::with('user');
        
        // Apply filters
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('blood_type') && $request->blood_type !== '') {
            $query->where('blood_type', $request->blood_type);
        }
        
        if ($request->filled('screening') && $request->screening !== '') {
            if ($request->screening === 'completed') {
                $query->whereNotNull('screening_answers');
            } elseif ($request->screening === 'pending') {
                $query->whereNull('screening_answers');
            }
        }
        
        // Apply search
        if ($request->filled('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('blood_type', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('donor_name', 'LIKE', "%{$search}%")
                  ->orWhere('donor_email', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        $donations = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // If it's an AJAX request with limit parameter (dashboard request), return limited data
        if ($request->ajax() && $request->has('limit')) {
            $limitedDonations = BloodDonation::with('user')
                ->orderBy('created_at', 'desc')
                ->take($request->limit)
                ->get();
            
            return response()->json([
                'donations' => $limitedDonations
            ]);
        }
        
        // If it's a regular AJAX request (admin page), return paginated data
        if ($request->ajax()) {
            return response()->json([
                'donations' => $donations->items(),
                'pagination' => [
                    'current_page' => $donations->currentPage(),
                    'last_page' => $donations->lastPage(),
                    'per_page' => $donations->perPage(),
                    'total' => $donations->total(),
                    'prev_page_url' => $donations->previousPageUrl(),
                    'next_page_url' => $donations->nextPageUrl(),
                ]
            ]);
        }
        
        return view('admin.blood-donations', compact('donations'));
    }

    /**
     * Update blood donation status.
     */
    public function updateDonationStatus(Request $request, $id)
    {
        try {
        $donation = BloodDonation::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,completed,rejected',
            'notes' => 'nullable|string',
                'admin_notes' => 'nullable|string',
                'screening_status' => 'nullable|string|in:pending,passed,failed',
                'quantity' => 'nullable|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
            return back()->withErrors($validator);
        }

        try {
                $donation->update($request->only(['status', 'notes', 'admin_notes', 'screening_status', 'quantity']));
                
                // Send notification to user about status change
                $this->notifyUserOfDonationStatusChange($donation, $request->status, $request->notes);
                
                // If completed, add to blood bank inventory
                if ($request->status === 'completed') {
                    $this->addDonationToInventory($donation);
                }
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Donation status updated successfully!',
                        'donation' => $donation->fresh()
                    ]);
            }
            
            return back()->with('status', 'Donation status updated successfully!');
        } catch (\Exception $e) {
                \Log::error('Failed to update donation status: ' . $e->getMessage());
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update donation status.'
                    ], 500);
                }
                
            return back()->withErrors(['error' => 'Failed to update donation status.']);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to find donation: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donation not found.'
                ], 404);
            }
            
            return back()->withErrors(['error' => 'Donation not found.']);
        }
    }

    /**
     * Show all appointments.
     */
    public function appointments(Request $request)
    {
        \Log::info('Appointments method called', [
            'ajax' => $request->ajax(),
            'limit' => $request->get('limit'),
            'filters' => $request->all()
        ]);
        
        try {
            $query = Appointment::with('user');
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('type')) {
                $query->where('appointment_type', $request->type);
            }
            
            if ($request->filled('date')) {
                $query->whereDate('appointment_date', $request->date);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('appointment_type', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
                });
            }
            
            $appointments = $query->orderBy('appointment_date', 'asc')->paginate(15);
            
            \Log::info('Appointments loaded with filters', [
                'count' => $appointments->count(), 
                'total' => $appointments->total(),
                'filters_applied' => $request->only(['status', 'type', 'date', 'search'])
            ]);
        
        // If it's an AJAX request with limit parameter (dashboard request), return limited data
        if ($request->ajax() && $request->has('limit')) {
                $limitedAppointments = $query->take($request->limit)->get();
                
                \Log::info('Returning limited appointments', ['count' => $limitedAppointments->count()]);
            
            return response()->json([
                'appointments' => $limitedAppointments
            ]);
        }
        
        // If it's a regular AJAX request (admin page), return paginated data
        if ($request->ajax()) {
                \Log::info('Returning paginated appointments', [
                    'current_page' => $appointments->currentPage(),
                    'total' => $appointments->total()
                ]);
                
            return response()->json([
                'appointments' => $appointments->items(),
                'pagination' => [
                    'current_page' => $appointments->currentPage(),
                    'last_page' => $appointments->lastPage(),
                    'per_page' => $appointments->perPage(),
                    'total' => $appointments->total(),
                    'prev_page_url' => $appointments->previousPageUrl(),
                    'next_page_url' => $appointments->nextPageUrl(),
                ]
            ]);
        }
        
        return view('admin.appointments', compact('appointments'));
        } catch (\Exception $e) {
            \Log::error('Error in appointments method: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to load appointments: ' . $e->getMessage()], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to load appointments: ' . $e->getMessage()]);
        }
    }

    /**
     * Show appointment details.
     */
    public function appointmentDetails($id)
    {
        \Log::info('Appointment details requested for ID: ' . $id);
        
        try {
        $appointment = Appointment::with('user')->findOrFail($id);
            \Log::info('Appointment found:', ['id' => $appointment->id, 'type' => $appointment->appointment_type]);
        
        // If it's an AJAX request, return JSON
        if (request()->ajax()) {
                \Log::info('Returning JSON response for appointment ID: ' . $id);
            return response()->json($appointment);
        }
        
        return view('admin.appointment-details', compact('appointment'));
        } catch (\Exception $e) {
            \Log::error('Error in appointmentDetails: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if (request()->ajax()) {
                return response()->json(['error' => 'Appointment not found'], 404);
            }
            
            abort(404, 'Appointment not found');
        }
    }

    /**
     * Show the edit form for an appointment.
     */
    public function editAppointment($id)
    {
        $appointment = Appointment::with('user')->findOrFail($id);
        $appointmentTypes = ['donation', 'screening', 'consultation', 'follow_up'];
        $timeSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30'
        ];
        
        return view('appointments.edit', compact('appointment', 'appointmentTypes', 'timeSlots'));
    }

    /**
     * Update an appointment.
     */
    public function updateAppointment(Request $request, $id)
    {
        \Log::info('Appointment update attempt', [
            'appointment_id' => $id,
            'request_data' => $request->all(),
            'is_ajax' => $request->ajax()
        ]);
        
        try {
            $appointment = Appointment::with('user')->findOrFail($id);
            \Log::info('Appointment found for update', [
                'appointment_id' => $appointment->id,
                'current_type' => $appointment->appointment_type
            ]);
        
        $validator = Validator::make($request->all(), [
            'appointment_type' => 'required|string|max:50',
            'blood_type' => 'nullable|string|max:5',
                'appointment_date' => 'required|date',
            'time_slot' => 'required|string|max:20',
                'status' => 'nullable|in:pending,confirmed,completed,cancelled,rejected',
            'notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
                \Log::warning('Validation failed for appointment update', [
                    'appointment_id' => $id,
                    'errors' => $validator->errors()
                ]);
                
                // Check if it's an AJAX request (multiple ways to detect)
                $isAjax = $request->ajax() || 
                          $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                          $request->header('Accept') === 'application/json' ||
                          $request->wantsJson();
                
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }
            return back()->withErrors($validator)->withInput();
        }

            \Log::info('Updating appointment', [
                'appointment_id' => $id,
                'new_type' => $request->appointment_type,
                'new_date' => $request->appointment_date
            ]);

            $updateData = $request->only([
                'appointment_type', 'blood_type', 'appointment_date', 'time_slot', 
                'notes', 'admin_notes'
            ]);
            
            // Only update status if it's provided
            if ($request->has('status') && $request->status !== null) {
                $updateData['status'] = $request->status;
            }
            
            $appointment->update($updateData);

            \Log::info('Appointment updated successfully', [
                'appointment_id' => $id
            ]);

            // Check if it's an AJAX request (multiple ways to detect)
            $isAjax = $request->ajax() || 
                      $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                      $request->header('Accept') === 'application/json' ||
                      $request->wantsJson();
            
            \Log::info('AJAX detection for update', [
                'ajax()' => $request->ajax(),
                'X-Requested-With' => $request->header('X-Requested-With'),
                'Accept' => $request->header('Accept'),
                'wantsJson()' => $request->wantsJson(),
                'final_detection' => $isAjax
            ]);
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment updated successfully!'
                ]);
            }

            return redirect()->route('admin.appointments')->with('status', 'Appointment updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if it's an AJAX request (multiple ways to detect)
            $isAjax = $request->ajax() || 
                      $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                      $request->header('Accept') === 'application/json' ||
                      $request->wantsJson();
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update appointment: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update appointment. Please try again.'])->withInput();
        }
    }

    /**
     * Update appointment status.
     */
    public function updateAppointmentStatus(Request $request, $id)
    {
        \Log::info('Appointment status update attempt', [
            'appointment_id' => $id,
            'request_data' => $request->all(),
            'user_agent' => $request->userAgent()
        ]);
        
        try {
            $appointment = Appointment::with('user')->findOrFail($id);
            \Log::info('Appointment found for status update', [
                'appointment_id' => $appointment->id,
                'current_status' => $appointment->status,
                'user_id' => $appointment->user_id
            ]);
        
        $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,confirmed,cancelled,completed,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
                \Log::warning('Validation failed for appointment status update', [
                    'appointment_id' => $id,
                    'errors' => $validator->errors()
                ]);
                
                // Check if it's an AJAX request (multiple ways to detect)
                $isAjax = $request->ajax() || 
                          $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                          $request->header('Accept') === 'application/json' ||
                          $request->wantsJson();
                
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }
            return back()->withErrors($validator);
        }

            \Log::info('Updating appointment status', [
                'appointment_id' => $id,
                'old_status' => $appointment->status,
                'new_status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            $appointment->update($request->only(['status', 'admin_notes']));
            
            \Log::info('Appointment status updated successfully', [
                'appointment_id' => $id,
                'new_status' => $appointment->fresh()->status
            ]);
            
            // Send notification to user about status change
            $this->notifyUserOfAppointmentStatusChange($appointment, $request->status, $request->admin_notes);
            
            // Check if it's an AJAX request (multiple ways to detect)
            $isAjax = $request->ajax() || 
                      $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                      $request->header('Accept') === 'application/json' ||
                      $request->wantsJson();
            
            \Log::info('AJAX detection', [
                'ajax()' => $request->ajax(),
                'X-Requested-With' => $request->header('X-Requested-With'),
                'Accept' => $request->header('Accept'),
                'wantsJson()' => $request->wantsJson(),
                'final_detection' => $isAjax
            ]);
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment status updated successfully!'
                ]);
            }
            
            return back()->with('status', 'Appointment status updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Error updating appointment status', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if it's an AJAX request (multiple ways to detect)
            $isAjax = $request->ajax() || 
                      $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                      $request->header('Accept') === 'application/json' ||
                      $request->wantsJson();
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update appointment status: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Failed to update appointment status.']);
        }
    }

    /**
     * Reschedule an appointment.
     */
    public function rescheduleAppointment(Request $request, $id)
    {
        \Log::info('Appointment reschedule attempt', [
            'appointment_id' => $id,
            'request_data' => $request->all()
        ]);
        
        try {
            $appointment = Appointment::with('user')->findOrFail($id);
            \Log::info('Appointment found for reschedule', [
                'appointment_id' => $appointment->id,
                'current_date' => $appointment->appointment_date,
                'current_time' => $appointment->time_slot
            ]);
            
            $validator = Validator::make($request->all(), [
                'appointment_date' => 'required|date',
                'time_slot' => 'required|string',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                \Log::warning('Validation failed for appointment reschedule', [
                    'appointment_id' => $id,
                    'errors' => $validator->errors()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            \Log::info('Rescheduling appointment', [
                'appointment_id' => $id,
                'new_date' => $request->appointment_date,
                'new_time' => $request->time_slot,
                'notes' => $request->notes
            ]);

            $appointment->update([
                'appointment_date' => $request->appointment_date,
                'time_slot' => $request->time_slot,
                'notes' => $request->notes,
                'status' => 'confirmed' // Reset to confirmed when rescheduled
            ]);
            
            \Log::info('Appointment rescheduled successfully', [
                'appointment_id' => $id,
                'new_date' => $appointment->fresh()->appointment_date
            ]);
            
            $this->notifyUserOfAppointmentStatusChange($appointment, 'confirmed', 'Appointment has been rescheduled. New date: ' . $request->appointment_date . ' at ' . $request->time_slot);
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment rescheduled successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error rescheduling appointment', [
                'appointment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reschedule appointment: ' . $e->getMessage()
                ], 500);
        }
    }

    /**
     * Get appointment statistics.
     */
    public function getAppointmentStats()
    {
        try {
            $stats = [
                'total' => Appointment::count(),
                'pending' => Appointment::where('status', 'pending')->count(),
                'confirmed' => Appointment::where('status', 'confirmed')->count(),
                'completed' => Appointment::where('status', 'completed')->count(),
                'cancelled' => Appointment::where('status', 'cancelled')->count(),
                'rejected' => Appointment::where('status', 'rejected')->count(),
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load appointment statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export appointments to CSV.
     */
    public function exportAppointments(Request $request)
    {
        try {
            $query = Appointment::with('user');
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('type')) {
                $query->where('appointment_type', $request->type);
            }
            if ($request->filled('date')) {
                $query->whereDate('appointment_date', $request->date);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                                 ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('appointment_type', 'LIKE', "%{$search}%");
                });
            }
            
            $appointments = $query->orderBy('appointment_date', 'asc')->get();
            
            $filename = 'appointments_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($appointments) {
                $file = fopen('php://output', 'w');
                
                // CSV headers - using actual database fields
                fputcsv($file, [
                    'ID', 'Patient Name', 'Patient Email', 'Type', 'Date', 'Time', 
                    'Blood Type', 'Status', 'Notes', 'Admin Notes', 'Created At'
                ]);
                
                // CSV data - using actual database fields
                foreach ($appointments as $appointment) {
                    fputcsv($file, [
                        $appointment->id,
                        $appointment->user ? $appointment->user->name : 'N/A',
                        $appointment->user ? $appointment->user->email : 'N/A',
                        $appointment->appointment_type,
                        $appointment->appointment_date ? $appointment->appointment_date->format('Y-m-d') : 'N/A',
                        $appointment->time_slot ?: 'N/A',
                        $appointment->blood_type ?: 'N/A',
                        $appointment->status,
                        $appointment->notes ?: 'N/A',
                        $appointment->admin_notes ?: 'N/A',
                        $appointment->created_at ? $appointment->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export appointments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show blood inventory.
     */
    public function bloodInventory()
    {
        $inventory = BloodBank::with('donor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $inventoryStats = [];
        
        foreach ($bloodTypes as $type) {
            $inventoryStats[$type] = [
                'available' => BloodBank::available()->where('blood_type', $type)->sum('quantity'),
                'pending' => BloodBank::pending()->where('blood_type', $type)->sum('quantity'),
                'expired' => BloodBank::expired()->where('blood_type', $type)->sum('quantity'),
            ];
        }
        
        return view('admin.blood-inventory', compact('inventory', 'inventoryStats'));
    }

    /**
     * Get dashboard statistics for AJAX requests.
     */
    public function getDashboardStats()
    {
        $stats = [
            'total_users' => User::count(),
            'total_requests' => BloodRequest::count(),
            'pending_requests' => BloodRequest::pending()->count(),
            'critical_requests' => BloodRequest::critical()->count(),
            'total_donations' => BloodDonation::count(),
            'pending_donations' => BloodDonation::pending()->count(),
            'total_appointments' => Appointment::count(),
            'today_appointments' => Appointment::today()->count(),
            'total_blood_units' => BloodBank::available()->sum('quantity'),
            'expired_blood' => BloodBank::expired()->sum('quantity'),
        ];
        
        return response()->json($stats);
    }

    /**
     * Get blood request status counts for the admin dashboard.
     */
    public function getBloodRequestStatusCounts()
    {
        try {
            $counts = [
                'pending' => BloodRequest::where('status', 'pending')->count(),
                'approved' => BloodRequest::where('status', 'approved')->count(),
                'completed' => BloodRequest::where('status', 'completed')->count(),
                'rejected' => BloodRequest::where('status', 'rejected')->count(),
                'cancelled' => BloodRequest::where('status', 'cancelled')->count(),
                'total' => BloodRequest::count(),
            ];
            
            return response()->json($counts);
        } catch (\Exception $e) {
            \Log::error('Failed to get blood request status counts: ' . $e->getMessage());
            return response()->json([
                'pending' => 0,
                'approved' => 0,
                'completed' => 0,
                'rejected' => 0,
                'cancelled' => 0,
                'total' => 0,
            ], 500);
        }
    }

    /**
     * Get blood donation status counts for the admin dashboard.
     */
    public function getBloodDonationStatusCounts()
    {
        try {
            $counts = [
                'pending' => BloodDonation::where('status', 'pending')->count(),
                'approved' => BloodDonation::where('status', 'approved')->count(),
                'completed' => BloodDonation::where('status', 'completed')->count(),
                'rejected' => BloodDonation::where('status', 'rejected')->count(),
                'cancelled' => BloodDonation::where('status', 'cancelled')->count(),
                'total' => BloodDonation::count(),
            ];
            
            return response()->json($counts);
        } catch (\Exception $e) {
            \Log::error('Failed to get blood donation status counts: ' . $e->getMessage());
            return response()->json([
                'pending' => 0,
                'approved' => 0,
                'completed' => 0,
                'rejected' => 0,
                'cancelled' => 0,
                'total' => 0,
            ], 500);
        }
    }

    /**
     * Get user statistics for AJAX requests.
     */
    public function getUserStats()
    {
        $stats = [
            'active_users' => User::where('is_verified', 1)->count(),
            'deactivated_users' => User::where('is_verified', 0)->count(),
            'total_users' => User::count(),
            'admin_users' => User::where('usertype', 'admin')->count(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Notify user of status change for blood requests.
     */
    private function notifyUserOfStatusChange($bloodRequest, $newStatus, $adminNotes = null)
    {
        $user = $bloodRequest->user;
        $statusText = ucfirst($newStatus);
        
        // Log the notification for debugging
        \Log::info('Status change notification sent', [
            'user_id' => $user->USER_ID,
            'request_id' => $bloodRequest->id,
            'old_status' => $bloodRequest->status,
            'new_status' => $newStatus,
            'admin_notes' => $adminNotes
        ]);
        
        // Send email notification
        try {
            NotificationService::sendRequestStatusUpdate($bloodRequest->id, $newStatus, $adminNotes);
            \Log::info("Email notification sent for blood request ID: {$bloodRequest->id}");
        } catch (\Exception $e) {
            \Log::error("Failed to send email notification: " . $e->getMessage());
        }
    }

    /**
     * Notify user of status change for blood donations.
     */
    private function notifyUserOfDonationStatusChange($bloodDonation, $newStatus, $notes = null)
    {
        $user = $bloodDonation->user;
        $statusText = ucfirst($newStatus);
        
        // Log the notification for debugging
        \Log::info('Donation status change notification sent', [
            'user_id' => $user->USER_ID,
            'donation_id' => $bloodDonation->id,
            'old_status' => $bloodDonation->status,
            'new_status' => $newStatus,
            'notes' => $notes
        ]);
        
        // Send email notification
        try {
            NotificationService::sendDonationStatusUpdate($bloodDonation->id, $newStatus, $notes);
            \Log::info("Email notification sent for blood donation ID: {$bloodDonation->id}");
        } catch (\Exception $e) {
            \Log::error("Failed to send email notification: " . $e->getMessage());
        }
    }

    /**
     * Notify user of status change for appointments.
     */
    private function notifyUserOfAppointmentStatusChange($appointment, $newStatus, $adminNotes = null)
    {
        $user = $appointment->user;
        $statusText = ucfirst($newStatus);
        
        // Log the notification for debugging
        \Log::info('Appointment status change notification sent', [
            'user_id' => $user->USER_ID,
            'appointment_id' => $appointment->id,
            'old_status' => $appointment->status,
            'new_status' => $newStatus,
            'admin_notes' => $adminNotes
        ]);
        
        // Send email notification based on status
        try {
            if ($newStatus === 'confirmed') {
                NotificationService::sendAppointmentConfirmation($appointment->id);
            } elseif ($newStatus === 'rejected') {
                NotificationService::sendAppointmentRejection($appointment->id, $adminNotes);
            } else {
                NotificationService::sendAppointmentStatusUpdate($appointment->id, $newStatus, $adminNotes);
            }
            
            \Log::info("Email notification sent for appointment ID: {$appointment->id}");
        } catch (\Exception $e) {
            \Log::error("Failed to send email notification: " . $e->getMessage());
        }
    }

    /**
     * Add completed blood donation to inventory.
     */
    private function addDonationToInventory($donation)
    {
        try {
            DB::beginTransaction();
            
            // Check if donation already exists in inventory
            $existingInventory = BloodBank::where('donor', $donation->user_id)
                ->where('blood_type', $donation->blood_type)
                ->where('acquisition_date', $donation->donation_date)
                ->first();
            
            if ($existingInventory) {
                \Log::info("Donation already exists in inventory: {$donation->id}");
                DB::rollBack();
                return;
            }
            
            // Calculate expiration date (blood typically expires after 42 days)
            $expirationDate = now()->addDays(42);
            
            // Add to blood bank inventory
            BloodBank::create([
                'donor' => $donation->user_id, // This will be null if no user_id
                'blood_type' => $donation->blood_type,
                'acquisition_date' => $donation->donation_date,
                'expiration_date' => $expirationDate,
                'quantity' => 1, // Each donation is typically 1 unit
                'status' => 1, // 1 = Available
            ]);
            
            \Log::info("Blood donation added to inventory: {$donation->id}");
            DB::commit();
            
        } catch (\Exception $e) {
            \Log::error("Failed to add donation to inventory: " . $e->getMessage());
            DB::rollBack();
        }
    }

    /**
     * Reduce blood inventory when a blood request is completed.
     */
    private function reduceBloodInventory($bloodRequest)
    {
        try {
            DB::beginTransaction();
            
            // Determine how many units to reduce
            $unitsToReduce = $bloodRequest->allocated_units ?? $bloodRequest->units_needed;
            
            \Log::info("Starting inventory reduction", [
                'request_id' => $bloodRequest->id,
                'allocated_units' => $bloodRequest->allocated_units,
                'units_needed' => $bloodRequest->units_needed,
                'units_to_reduce' => $unitsToReduce,
                'blood_type' => $bloodRequest->blood_type
            ]);
            
            if (!$unitsToReduce || $unitsToReduce <= 0) {
                \Log::warning("No units to reduce for blood request: {$bloodRequest->id}");
                DB::rollBack();
                return;
            }
            
            // Find available blood inventory of the matching type, ordered by oldest first (FIFO)
            $availableInventory = BloodBank::where('blood_type', $bloodRequest->blood_type)
                ->where('status', 1) // Available
                ->where('quantity', '>', 0)
                ->where('expiration_date', '>', now()) // Not expired
                ->orderBy('acquisition_date', 'asc') // Oldest first
                ->get();
            
            \Log::info("Found available inventory", [
                'request_id' => $bloodRequest->id,
                'blood_type' => $bloodRequest->blood_type,
                'inventory_items_count' => $availableInventory->count(),
                'total_available_quantity' => $availableInventory->sum('quantity'),
                'units_required' => $unitsToReduce
            ]);
            
            if ($availableInventory->sum('quantity') < $unitsToReduce) {
                \Log::error("Insufficient blood inventory for request: {$bloodRequest->id}. Required: {$unitsToReduce}, Available: " . $availableInventory->sum('quantity'));
                DB::rollBack();
                throw new \Exception("Insufficient blood inventory. Required: {$unitsToReduce} units, Available: " . $availableInventory->sum('quantity') . " units.");
            }
            
            $remainingUnits = $unitsToReduce;
            
            \Log::info("Starting inventory reduction process", [
                'request_id' => $bloodRequest->id,
                'total_units_to_reduce' => $unitsToReduce,
                'remaining_units' => $remainingUnits
            ]);
            
            // Reduce inventory using FIFO principle
            foreach ($availableInventory as $inventory) {
                if ($remainingUnits <= 0) break;
                
                $unitsFromThisInventory = min($remainingUnits, $inventory->quantity);
                $oldQuantity = $inventory->quantity;
                $inventory->quantity -= $unitsFromThisInventory;
                $remainingUnits -= $unitsFromThisInventory;
                
                // If this inventory item is now empty, mark it as unavailable
                if ($inventory->quantity <= 0) {
                    $inventory->status = 0; // 0 = Unavailable
                }
                
                $inventory->save();
                
                \Log::info("Reduced inventory item", [
                    'inventory_id' => $inventory->STOCK_ID,
                    'old_quantity' => $oldQuantity,
                    'units_reduced' => $unitsFromThisInventory,
                    'new_quantity' => $inventory->quantity,
                    'new_status' => $inventory->status,
                    'remaining_units_to_reduce' => $remainingUnits
                ]);
            }
            
            if ($remainingUnits > 0) {
                \Log::error("Failed to reduce all required units. Remaining: {$remainingUnits}");
                DB::rollBack();
                throw new \Exception("Failed to reduce all required blood units. Remaining: {$remainingUnits}");
            }
            
            \Log::info("Successfully reduced blood inventory for request: {$bloodRequest->id}. Units reduced: {$unitsToReduce}");
            DB::commit();
            
        } catch (\Exception $e) {
            \Log::error("Failed to reduce blood inventory: " . $e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Export blood donations to CSV.
     */
    public function exportDonations(Request $request)
    {
        try {
            $query = BloodDonation::with('user');
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('blood_type')) {
                $query->where('blood_type', $request->blood_type);
            }
            if ($request->filled('date')) {
                $query->whereDate('donation_date', $request->date);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('blood_type', 'LIKE', "%{$search}%");
                });
            }
            
            $donations = $query->orderBy('donation_date', 'desc')->get();
            
            $filename = 'blood_donations_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($donations) {
                $file = fopen('php://output', 'w');
                
                // CSV headers - using actual database fields
                fputcsv($file, [
                    'ID', 'Donor Name', 'Donor Email', 'Blood Type', 'Donation Date', 
                    'Quantity (ml)', 'Status', 'Screening Status', 'Notes', 'Admin Notes', 'Created At'
                ]);
                
                // CSV data - using actual database fields
                foreach ($donations as $donation) {
                    fputcsv($file, [
                        $donation->id,
                        $donation->donor_name ?: 'N/A',
                        $donation->donor_email ?: 'N/A',
                        $donation->blood_type ?: 'N/A',
                        $donation->donation_date ? $donation->donation_date->format('Y-m-d') : 'N/A',
                        $donation->quantity ?: 'N/A',
                        $donation->status,
                        $donation->screening_status ?: 'N/A',
                        $donation->notes ?: 'N/A',
                        $donation->admin_notes ?: 'N/A',
                        $donation->created_at ? $donation->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export blood donations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export blood requests to CSV.
     */
    public function exportRequests(Request $request)
    {
        try {
            $query = BloodRequest::with('user');
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('blood_type')) {
                $query->where('blood_type', $request->blood_type);
            }
            if ($request->filled('urgency')) {
                $query->where('urgency', $request->urgency);
            }
            if ($request->filled('date')) {
                $query->whereDate('request_date', $request->date);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('blood_type', 'LIKE', "%{$search}%");
                });
            }
            
            $requests = $query->orderBy('request_date', 'desc')->get();
            
            $filename = 'blood_requests_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($requests) {
                $file = fopen('php://output', 'w');
                
                // CSV headers - using actual database fields
                fputcsv($file, [
                    'ID', 'Patient Name', 'Patient Email', 'Blood Type', 'Request Date', 
                    'Units Needed', 'Allocated Units', 'Urgency', 'Reason', 'Hospital', 'Contact Person', 'Contact Number', 'Status', 'Notes', 'Admin Notes', 'Created At'
                ]);
                
                // CSV data - using actual database fields
                foreach ($requests as $request) {
                    fputcsv($file, [
                        $request->id,
                        $request->user ? $request->user->name : 'N/A',
                        $request->user ? $request->user->email : 'N/A',
                        $request->blood_type ?: 'N/A',
                        $request->request_date ? $request->request_date->format('Y-m-d') : 'N/A',
                        $request->units_needed ?: 'N/A',
                        $request->allocated_units ?: 'N/A',
                        $request->urgency ?: 'N/A',
                        $request->reason ?: 'N/A',
                        $request->hospital ?: 'N/A',
                        $request->contact_person ?: 'N/A',
                        $request->contact_number ?: 'N/A',
                        $request->status,
                        $request->additional_notes ?: 'N/A',
                        $request->admin_notes ?: 'N/A',
                        $request->created_at ? $request->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export blood requests: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users to CSV.
     */
    public function exportUsers(Request $request)
    {
        try {
            $query = User::query();
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('is_verified', $request->status === 'verified' ? 1 : 0);
            }
            if ($request->filled('role')) {
                $query->where('usertype', $request->role);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('contact', 'LIKE', "%{$search}%");
                });
            }
            
            $users = $query->orderBy('created_at', 'desc')->get();
            
            $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                
                // CSV headers - using actual database fields
                fputcsv($file, [
                    'User ID', 'Name', 'Email', 'Contact', 'User Type', 'Email Verified', 'Blood Type', 
                    'Date of Birth', 'Sex', 'Address', 'City', 'Province', 'Schedule Date', 'Last Donation Date', 'Created At'
                ]);
                
                // CSV data - using actual database fields
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->USER_ID,
                        $user->name ?: 'N/A',
                        $user->email ?: 'N/A',
                        $user->contact ?: 'N/A',
                        $user->usertype ?: 'N/A',
                        $user->is_verified ? 'Yes' : 'No',
                        $user->bloodtype ?: 'N/A',
                        $user->dob ?: 'N/A',
                        $user->sex ?: 'N/A',
                        $user->address ?: 'N/A',
                        $user->city ?: 'N/A',
                        $user->province ?: 'N/A',
                        $user->schedule_date ?: 'N/A',
                        $user->last_donation_date ?: 'N/A',
                        $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export users: ' . $e->getMessage()
            ], 500);
        }
    }
}
