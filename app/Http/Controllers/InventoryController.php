<?php

namespace App\Http\Controllers;

use App\Models\BloodBank;
use App\Models\BloodRequest;
use App\Models\BloodDonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
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
     * Show blood inventory dashboard.
     */
    public function index()
    {
        // Get overall inventory statistics
        $totalUnits = BloodBank::sum('quantity');
        $availableUnits = BloodBank::available()->sum('quantity');
        $pendingUnits = BloodBank::pending()->sum('quantity');
        $expiredUnits = BloodBank::expired()->sum('quantity');
        
        // Get inventory by blood type
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $inventoryByType = [];
        
        foreach ($bloodTypes as $type) {
            $inventoryByType[$type] = [
                'total' => BloodBank::where('blood_type', $type)->sum('quantity'),
                'available' => BloodBank::available()->where('blood_type', $type)->sum('quantity'),
                'pending' => BloodBank::pending()->where('blood_type', $type)->sum('quantity'),
                'expired' => BloodBank::expired()->where('blood_type', $type)->sum('quantity'),
            ];
        }
        
        // Get recent inventory activities
        $recentDonations = BloodDonation::with('user')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get expiring soon blood units
        $expiringSoon = BloodBank::available()
            ->where('expiration_date', '<=', now()->addDays(30))
            ->where('expiration_date', '>', now())
            ->orderBy('expiration_date', 'asc')
            ->take(10)
            ->get();
        
        // Get low stock alerts
        $lowStockAlerts = [];
        foreach ($bloodTypes as $type) {
            $available = BloodBank::available()->where('blood_type', $type)->sum('quantity');
            if ($available <= 5) {
                $lowStockAlerts[$type] = $available;
            }
        }
        
        return view('inventory.index', compact(
            'totalUnits',
            'availableUnits',
            'pendingUnits',
            'expiredUnits',
            'inventoryByType',
            'recentDonations',
            'expiringSoon',
            'lowStockAlerts'
        ));
    }

    /**
     * Show detailed inventory list.
     */
    public function list(Request $request)
    {
        try {
            // Use leftJoin to handle cases where donor relationship might fail
            $query = BloodBank::leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
                             ->select('blood_banks.*', 'users.name as donor_name', 'users.USER_ID as donor_user_id');
            
            // Apply filters
            if ($request->filled('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('blood_type') && $request->blood_type !== '') {
                $query->where('blood_type', $request->blood_type);
            }
            
            if ($request->filled('expiration') && $request->expiration !== '') {
                if ($request->expiration === '7') {
                    $query->where('expiration_date', '<=', now()->addDays(7))
                          ->where('expiration_date', '>', now());
                }
            }
            
            if ($request->filled('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('blood_banks.STOCK_ID', 'LIKE', "%{$search}%")
                      ->orWhere('blood_banks.blood_type', 'LIKE', "%{$search}%")
                      ->orWhere('users.name', 'LIKE', "%{$search}%")
                      ->orWhere('users.email', 'LIKE', "%{$search}%");
                });
            }
            
            $inventory = $query->orderBy('created_at', 'desc')->paginate(20);
            
            // Log some debug information
            if (config('app.debug')) {
                \Log::info('Inventory list loaded', [
                    'total_items' => $inventory->total(),
                    'current_page' => $inventory->currentPage(),
                    'sample_donor_data' => $inventory->first() ? [
                        'donor_field' => $inventory->first()->donor,
                        'donor_name' => $inventory->first()->donor_name,
                        'donor_user_id' => $inventory->first()->donor_user_id
                    ] : null
                ]);
            }
            
            // If it's an AJAX request, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'inventory' => $inventory->items(),
                    'pagination' => [
                        'current_page' => $inventory->currentPage(),
                        'last_page' => $inventory->lastPage(),
                        'per_page' => $inventory->perPage(),
                        'total' => $inventory->total(),
                        'prev_page_url' => $inventory->previousPageUrl(),
                        'next_page_url' => $inventory->nextPageUrl(),
                    ]
                ]);
            }
            
            return view('inventory.list', compact('inventory'));
        } catch (\Exception $e) {
            \Log::error('Error loading inventory list: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load inventory list',
                    'message' => $e->getMessage()
                ], 500);
            }
            
            abort(500, 'Failed to load inventory list');
        }
    }

    /**
     * Show low stock blood units.
     */
    public function lowStock()
    {
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $lowStockData = [];
        
        foreach ($bloodTypes as $type) {
            $available = BloodBank::available()->where('blood_type', $type)->sum('quantity');
            if ($available <= 5) {
                $lowStockData[$type] = [
                    'available' => $available,
                    'units' => BloodBank::available()->where('blood_type', $type)->get()
                ];
            }
        }
        
        // Get low stock inventory (quantity <= 5)
        $inventory = BloodBank::available()
            ->leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
            ->select('blood_banks.*', 'users.name as donor_name', 'users.USER_ID as donor_user_id')
            ->where('quantity', '<=', 5)
            ->orderBy('quantity', 'asc')
            ->paginate(20);
        
        return view('inventory.low-stock', compact('inventory', 'lowStockData'));
    }

    /**
     * Show inventory by blood type.
     */
    public function byBloodType($bloodType)
    {
        $inventory = BloodBank::leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
            ->select('blood_banks.*', 'users.name as donor_name', 'users.USER_ID as donor_user_id')
            ->where('blood_type', $bloodType)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $bloodTypeStats = [
            'total' => BloodBank::where('blood_type', $bloodType)->sum('quantity'),
            'available' => BloodBank::available()->where('blood_type', $bloodType)->sum('quantity'),
            'pending' => BloodBank::pending()->where('blood_type', $bloodType)->sum('quantity'),
            'expired' => BloodBank::expired()->where('blood_type', $bloodType)->sum('quantity'),
        ];
        
        return view('inventory.by-blood-type', compact('inventory', 'bloodType', 'bloodTypeStats'));
    }

    /**
     * Show individual inventory item.
     */
    public function show($id)
    {
        try {
            // Use leftJoin to handle donor data safely
            $inventory = BloodBank::leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
                                 ->select('blood_banks.*', 'users.name as donor_name', 'users.USER_ID as donor_user_id')
                                 ->where('blood_banks.id', $id)
                                 ->first();
            
            if (!$inventory) {
                throw new \Exception('Inventory item not found');
            }
            
            // If it's an AJAX request, return JSON
            if (request()->ajax()) {
                return response()->json($inventory);
            }
            
            return view('inventory.show', compact('inventory'));
        } catch (\Exception $e) {
            \Log::error('Failed to find inventory item: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inventory item not found.'
                ], 404);
            }
            
            abort(404, 'Inventory item not found.');
        }
    }



    /**
     * Show expiring soon blood units.
     */
    public function expiring()
    {
        $inventory = BloodBank::available()
            ->leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
            ->select('blood_banks.*', 'users.name as donor_name', 'users.USER_ID as donor_user_id')
            ->where('expiration_date', '<=', now()->addDays(30))
            ->where('expiration_date', '>', now())
            ->orderBy('expiration_date', 'asc')
            ->paginate(20);
        
        return view('inventory.expiring', compact('inventory'));
    }

    /**
     * Show expired blood units.
     */
    public function expired()
    {
        $expiredUnits = BloodBank::expired()
            ->leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
            ->select('blood_banks.*', 'users.name as donor_name', 'users.USER_ID as donor_user_id')
            ->orderBy('expiration_date', 'desc')
            ->paginate(20);
        
        return view('inventory.expired', compact('expiredUnits'));
    }



    /**
     * Update blood unit status.
     */
    public function updateStatus(Request $request, $id)
    {
        $bloodUnit = BloodBank::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:-1,0,1',
            'notes' => 'nullable|string',
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
            $bloodUnit->update($request->only(['status']));
            
            // Log the status change
            \Log::info('Blood unit status updated', [
                'stock_id' => $bloodUnit->STOCK_ID,
                'new_status' => $request->status,
                'notes' => $request->notes
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Blood unit status updated successfully!',
                    'blood_unit' => $bloodUnit->fresh()
                ]);
            }
            
            return back()->with('status', 'Blood unit status updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to update blood unit status: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update blood unit status.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update blood unit status.']);
        }
    }

    /**
     * Remove expired blood units.
     */
    public function removeExpired(Request $request)
    {
        try {
            $expiredCount = BloodBank::expired()->count();
            BloodBank::expired()->delete();
            
            \Log::info("Removed {$expiredCount} expired blood units");
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully removed {$expiredCount} expired blood units.",
                    'removed_count' => $expiredCount
                ]);
            }
            
            return back()->with('status', "Successfully removed {$expiredCount} expired blood units.");
        } catch (\Exception $e) {
            \Log::error('Failed to remove expired blood units: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove expired blood units.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to remove expired blood units.']);
        }
    }

    /**
     * Get inventory statistics for AJAX requests.
     */
    public function getStats()
    {
        try {
            $totalUnits = BloodBank::sum('quantity');
            $availableUnits = BloodBank::available()->sum('quantity');
            $pendingUnits = BloodBank::pending()->sum('quantity');
            $expiredUnits = BloodBank::expired()->sum('quantity');
            
            $stats = [
                'total_units' => $totalUnits,
                'available_units' => $availableUnits,
                'pending_units' => $pendingUnits,
                'expired_units' => $expiredUnits,
                'expiring_soon' => BloodBank::available()
                    ->where('expiration_date', '<=', now()->addDays(30))
                    ->where('expiration_date', '>', now())
                    ->sum('quantity'),
                'low_stock' => 0, // Will calculate below
            ];
            
            // Get blood type breakdown
            $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            $bloodTypeStats = [];
            $lowStockCount = 0;
            $lowStockData = [];
            
            foreach ($bloodTypes as $type) {
                $available = BloodBank::available()->where('blood_type', $type)->sum('quantity');
                $total = BloodBank::where('blood_type', $type)->sum('quantity');
                
                $bloodTypeStats[$type] = [
                    'total' => $total,
                    'available' => $available,
                ];
                
                // Count low stock (5 units or less)
                if ($available <= 5) {
                    $lowStockCount++;
                    $lowStockData[$type] = [
                        'available' => $available
                    ];
                }
            }
            
            // Get expiring soon data (next 30 days) with details
            $expiringSoonData = BloodBank::available()
                ->leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
                ->select('blood_banks.blood_type', 'blood_banks.quantity', 'blood_banks.expiration_date', 'users.name as donor_name')
                ->where('expiration_date', '<=', now()->addDays(30))
                ->where('expiration_date', '>', now())
                ->orderBy('expiration_date', 'asc')
                ->take(10)
                ->get();
            
            $stats['blood_type_breakdown'] = $bloodTypeStats;
            $stats['low_stock'] = $lowStockCount;
            $stats['low_stock_data'] = $lowStockData;
            $stats['expiring_soon_data'] = $expiringSoonData;
            
            return response()->json($stats);
        } catch (\Exception $e) {
            \Log::error('Failed to get inventory stats: ' . $e->getMessage());
            return response()->json([
                'total_units' => 0,
                'available_units' => 0,
                'pending_units' => 0,
                'expired_units' => 0,
                'expiring_soon' => 0,
                'low_stock' => 0,
                'blood_type_breakdown' => [],
                'low_stock_data' => [],
                'expiring_soon_data' => [],
            ], 500);
        }
    }

    /**
     * Generate inventory report.
     */
    public function generateReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'report_type' => 'required|in:inventory,donations,requests,expiry',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $reportType = $request->report_type;

        switch ($reportType) {
            case 'inventory':
                $data = $this->generateInventoryReport($startDate, $endDate);
                break;
            case 'donations':
                $data = $this->generateDonationsReport($startDate, $endDate);
                break;
            case 'requests':
                $data = $this->generateRequestsReport($startDate, $endDate);
                break;
            case 'expiry':
                $data = $this->generateExpiryReport($startDate, $endDate);
                break;
            default:
                $data = [];
        }

        return view('inventory.report', compact('data', 'startDate', 'endDate', 'reportType'));
    }

    /**
     * Generate inventory report data.
     */
    private function generateInventoryReport($startDate, $endDate)
    {
        return BloodBank::leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
            ->select('blood_banks.*', 'users.name as donor_name')
            ->whereBetween('blood_banks.created_at', [$startDate, $endDate])
            ->get()
            ->groupBy('blood_type');
    }

    /**
     * Generate donations report data.
     */
    private function generateDonationsReport($startDate, $endDate)
    {
        return BloodDonation::whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->get()
            ->groupBy('status');
    }

    /**
     * Generate requests report data.
     */
    private function generateRequestsReport($startDate, $endDate)
    {
        return BloodRequest::whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->get()
            ->groupBy('status');
    }

    /**
     * Generate expiry report data.
     */
    private function generateExpiryReport($startDate, $endDate)
    {
        return BloodBank::leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
            ->select('blood_banks.*', 'users.name as donor_name')
            ->whereBetween('blood_banks.expiration_date', [$startDate, $endDate])
            ->get()
            ->groupBy('blood_type');
    }

    /**
     * Remove a specific inventory item.
     */
    public function destroy($id)
    {
        try {
            $inventory = BloodBank::findOrFail($id);
            $inventory->delete();
            
            \Log::info("Removed inventory item: {$id}");
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inventory item removed successfully.'
                ]);
            }
            
            return back()->with('status', 'Inventory item removed successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to remove inventory item: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove inventory item.'
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to remove inventory item.']);
        }
    }

    /**
     * Export inventory data to CSV.
     */
    public function export(Request $request)
    {
        try {
            \Log::info('Export request received', [
                'filters' => $request->all(),
                'user_agent' => $request->userAgent()
            ]);
            
            $query = BloodBank::leftJoin('users', 'blood_banks.donor', '=', 'users.USER_ID')
                             ->select('blood_banks.*', 'users.name as donor_name');
            
            // Apply filters
            if ($request->filled('blood_type')) {
                $query->where('blood_banks.blood_type', $request->blood_type);
                \Log::info('Applied blood_type filter', ['value' => $request->blood_type]);
            }
            
            if ($request->filled('status')) {
                $query->where('blood_banks.status', $request->status);
                \Log::info('Applied status filter', ['value' => $request->status]);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('blood_banks.blood_type', 'LIKE', "%{$search}%")
                      ->orWhere('users.name', 'LIKE', "%{$search}%");
                });
                \Log::info('Applied search filter', ['value' => $search]);
            }
            
            $inventory = $query->orderBy('blood_banks.created_at', 'desc')->get();
            
            \Log::info('Export query completed', [
                'total_records' => $inventory->count(),
                'sql' => $query->toSql()
            ]);
            
            $filename = 'blood_inventory_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($inventory) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'Stock ID', 'Blood Type', 'Donor Name', 'Quantity (ml)', 'Status', 
                    'Acquisition Date', 'Expiration Date', 'Created At'
                ]);
                
                // CSV data
                foreach ($inventory as $item) {
                    fputcsv($file, [
                        $item->STOCK_ID,
                        $item->blood_type ?: 'N/A',
                        $item->donor_name ?: 'N/A',
                        $item->quantity ?: 'N/A',
                        $this->getStatusText($item->status),
                        $item->acquisition_date ?: 'N/A',
                        $item->expiration_date ?: 'N/A',
                        $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to export inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status text from status code.
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 1:
                return 'Available';
            case 0:
                return 'Unavailable';
            case -1:
                return 'Expired';
            default:
                return 'Unknown';
        }
    }
}
