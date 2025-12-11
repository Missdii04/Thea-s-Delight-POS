<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class UserController extends Controller
{
    /**
     * Display a listing of the resource (R - Read) and the Cashier Performance Report.
     */
    public function index(Request $request): View
    {
        // --- 1. USER COUNTS (Static Metrics) ---
        $adminCount = User::where('role', 'admin')->count();
        $activeCashiers = User::where('role', 'cashier')->count();
        $totalUsers = User::count();

        // 2. Fetch the list of users (excluding self)
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->paginate(10);
        
        // 3. List of cashiers for the filter dropdown
        $cashiersForFilter = User::where('role', 'cashier')->select('id', 'name')->get(); 

        // --- 4. SALES & TRANSACTION REPORTING LOGIC ---
        
        $reportInterval = $request->input('report_interval', 'day');
        $selectedCashierId = $request->input('cashier_id');

        // Date Handling (Prioritizes custom dates)
        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');
        
        if ($customStartDate && $customEndDate) {
            $startDate = Carbon::parse($customStartDate)->startOfDay();
            $endDate = Carbon::parse($customEndDate)->endOfDay();
        } else {
            $endDate = Carbon::now()->endOfDay();
            $startDate = match ($reportInterval) {
                'week' => Carbon::now()->startOfWeek(),
                'month' => Carbon::now()->startOfMonth(),
                default => Carbon::now()->startOfDay(), // 'day'
            };
        }

        // Initialize variables to avoid Undefined Variable errors
        $cashierReport = collect([]);
        $detailedOrders = collect([]);

        if ($selectedCashierId) {
            // Case A: SPECIFIC CASHIER SELECTED -> Fetch Detailed Orders
            $detailedOrders = Order::query()
                ->where('cashier_id', $selectedCashierId) // Filter by selected cashier
                ->whereBetween('created_at', [$startDate, $endDate])
                ->with(['items'])
                ->get()
                ->map(function ($order) {
                    // Calculate the true gross total from items for display accuracy
                    $order->correct_total_amount = $order->items->sum('total'); 
                    return $order;
                });
            
        } else {
            // Case B: NO CASHIER SELECTED -> Fetch Aggregated Report
            $cashierReport = $this->getAggregatedCashierReport($startDate, $endDate);
        }

        // --- 5. RETURN VIEW ---
        return view('admin.users.index', compact(
            'users', 
            'adminCount', 
            'activeCashiers',
            'totalUsers', 
            'cashierReport', 
            'detailedOrders', // Passed for Case A
            'cashiersForFilter', // Passed for dropdown list
            'selectedCashierId', // Passed to maintain selection state
            'reportInterval', 
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Helper to fetch aggregated sales and transaction counts for all cashiers.
     */
    protected function getAggregatedCashierReport(Carbon $startDate, Carbon $endDate)
    {
        $cashierPerformance = Order::query()
            // CRITICAL FIX 1: Join the item details table to get accurate totals
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(
                'orders.cashier_id', 
                // CRITICAL FIX 2: Sum the 'total' column from the order_items table for true gross sales
                DB::raw('SUM(order_items.total) as total_sales'), 
                
                // CRITICAL FIX 3: Count distinct order IDs to get the transaction count
                DB::raw('COUNT(DISTINCT orders.id) as total_transactions')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            // NOTE: Removed ->where('status', 'completed') to show all statuses if needed in the report
            ->whereHas('cashier', function ($query) { 
                $query->where('role', 'cashier');
            })
            ->groupBy('orders.cashier_id') 
            ->with('cashier')
            ->get();

        return $cashierPerformance->map(function ($item) {
            $name = optional($item->cashier)->name ?? "Unknown Cashier ({$item->cashier_id})";
            return [
                'name' => $name,
                'total_sales' => $item->total_sales,
                'total_transactions' => $item->total_transactions,
            ];
        })->sortByDesc('total_sales')->values();
    }

    /**
     * Show the form for creating a new user (C - Create Form).
     */
    public function create()
    {
        return view('admin.users.create');
    }

    
    
    /**
     * Store a newly created resource in storage (C - Create Logic).
     */
    public function store(Request $request)
    {
        // ... (Your original store method logic) ...
    }
    
    /**
     * Show the form for editing the specified resource (U - Update Form).
     */
    public function edit(User $user)
    {
        // The model is resolved by the route automatically (Route Model Binding)

        return view('admin.users.edit', [
            'user' => $user, 
        ]);
    }
    
    /**
     * Update the specified resource in storage (U - Update Logic).
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // 1. Validation Logic
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), 
            ],
            'role' => ['required', Rule::in(['admin', 'cashier'])],
            'is_active' => ['required', 'boolean'],
        ]);

        // 2. Handle Admin Role/Active Status Protection
        if (Auth::user()->id === $user->id) {
            $validated['role'] = $user->role; 
            $validated['is_active'] = $user->is_active; 
        }

        // 3. Update the User Model
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => $validated['is_active'],
        ]);
        
        // --- CRITICAL FIX: Capture filters from the request query string ---
        $filters = $request->only(['cashier_id', 'start_date', 'end_date', 'report_interval']);

        // 4. Return Redirect Response, passing filters
        return redirect()->route('admin.users.index', $filters)
            ->with('success', 'User ' . $user->name . ' details updated successfully.');
    }
    
    /**
     * Remove the specified resource from storage (D - Delete Logic).
     */
    public function destroy(User $user)
    {
        // ... (Your original destroy method logic) ...
    }
    
    /**
     * Suspend/Deactivate a user.
     */
    public function deactivate(User $user)
    {
        // Safety check: Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => false]);

        // --- CRITICAL FIX: Capture filters from the request and redirect ---
        $filters = request()->only(['cashier_id', 'start_date', 'end_date', 'report_interval']);

        return redirect()->route('admin.users.index', $filters)
            ->with('success', $user->name . ' has been suspended/deactivated.');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        // --- CRITICAL FIX: Capture filters from the request and redirect ---
        $filters = request()->only(['cashier_id', 'start_date', 'end_date', 'report_interval']);

        return redirect()->route('admin.users.index', $filters)
            ->with('success', $user->name . ' has been reactivated.');
    }
}