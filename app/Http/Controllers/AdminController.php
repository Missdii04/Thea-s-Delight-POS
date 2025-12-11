<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Order;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Money\Money;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Currencies\ISOCurrencies; 


class AdminController extends Controller
{
    // Define the critical stock threshold as a constant
    private const LOW_STOCK_THRESHOLD = 5;
    private const PRODUCTS_PER_PAGE = 5; // For pagination
    private const VAT_RATE = 0.12;

    /**
     * Shows the Admin Dashboard Overview with live operational metrics.
     * Mapped to the route: 'dashboard'
     */
    // AFTER (Fix)
public function index(Request $request): View
{
    $today = Carbon::today();

    // NOTE: The Add-on categories array should contain the exact strings from your Enum/Seeder
    $addOnCategories = ['Cake Topper', 'Candles', 'Greeting Card']; // Simplified based on your provided list

    // --- All Metric Counts Logic ---
    $availableCakesCount = Product::where('stock_quantity', '>', 0)->whereNotIn('category', $addOnCategories)->count();
    $availableAddOnsCount = Product::where('stock_quantity', '>', 0)->whereIn('category', $addOnCategories)->count();
    $salesToday = Order::whereDate('created_at', $today)->where('status', 'completed')->sum('total_amount');
    $totalOrdersToday = Order::whereDate('created_at', $today)->where('status', 'completed')->count();
    $activeCashiers = User::where('role', 'cashier')->count();
    $lowStockProducts = Product::where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)->orderBy('stock_quantity', 'asc')->get();
    $lowStockCount = $lowStockProducts->count();
    
    // --- Stock Chart Data ---
    $cakeStockSummary = Product::select('name', 'stock_quantity')->whereNotIn('category', $addOnCategories)->orderBy('stock_quantity', 'desc')->pluck('stock_quantity', 'name');
    $addonStockSummary = Product::select('name', 'stock_quantity')->whereIn('category', $addOnCategories)->orderBy('stock_quantity', 'desc')->pluck('stock_quantity', 'name');

    $cakeLabels = $cakeStockSummary->keys()->toArray();
    $cakeData = $cakeStockSummary->values()->map(fn($val) => (int)$val)->toArray();
    $addonLabels = $addonStockSummary->keys()->toArray();
    $addonData = $addonStockSummary->values()->map(fn($val) => (int)$val)->toArray();

    // 1. DYNAMIC DATE & INTERVAL HANDLING
    $defaultStartDate = now()->subMonths(3)->startOfMonth();
    $defaultEndDate = now()->endOfDay();

    $startDate = $request->input('start_date') 
        ? Carbon::parse($request->input('start_date'))->startOfDay() 
        : $defaultStartDate;
        
    $endDate = $request->input('end_date') 
        ? Carbon::parse($request->input('end_date'))->endOfDay() 
        : $defaultEndDate;
        
    $interval = $request->input('interval', 'month');
    
    // Variables needed to pass the filter state back to the Blade form
    $selectedStartDate = $startDate->format('Y-m-d');
    $selectedEndDate = $endDate->format('Y-m-d');
    $selectedInterval = $interval;


    // --- DYNAMIC CHART DATA (Trend Analysis) ---

    // 2. BASE QUERY FOR TRENDS
    // We use Trend::query(Eloquent Builder) to apply the 'status' filter correctly.
    $baseTrendQuery = Order::query()->where('status', 'completed');
    // Setup the Trend instance for the selected date range
    $trendQuery = Trend::query($baseTrendQuery)->between(start: $startDate, end: $endDate);

    // 2.1. REVENUE TREND DATA
    $revenueTrend = match ($interval) {
        'day' => $trendQuery->perDay()->sum('total_amount'),
        'year' => $trendQuery->perYear()->sum('total_amount'),
        default => $trendQuery->perMonth()->sum('total_amount'),
    };
    
    $revenueLabels = $revenueTrend->map(fn (TrendValue $value) => $value->date)->toArray();
    $revenueData = $revenueTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray();

    // 2.2. TRANSACTION COUNT TREND DATA
    // Trend::query re-uses the $baseTrendQuery (which includes the 'completed' status filter)
    $transactionTrendQuery = Trend::query($baseTrendQuery)->between(start: $startDate, end: $endDate);

    $transactionTrend = match ($interval) {
        'day' => $transactionTrendQuery->perDay()->count(),
        'year' => $transactionTrendQuery->perYear()->count(),
        default => $transactionTrendQuery->perMonth()->count(),
    };

    // NOTE: We use the $revenueLabels for X-axis consistency
    $transactionData = $transactionTrend->map(fn (TrendValue $value) => $value->aggregate)->toArray();
    

    // 3. TOP-SELLING PRODUCTS DATA (Unchanged)
    $topSellers = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id') 
        ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
        ->whereBetween('order_items.created_at', [$startDate, $endDate])
        ->groupBy('products.name') 
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();

    $bestSellerLabels = $topSellers->map(fn ($item) => $item->name)->toArray();
    $bestSellerData = $topSellers->map(fn ($item) => $item->total_sold)->toArray();


    // 4. CASHIER PERFORMANCE DATA (Unchanged)
    $cashierPerformance = Order::query()
        ->select('cashier_id', DB::raw('SUM(total_amount) as total_sales'))
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('cashier_id')
        ->orderByDesc('total_sales')
        ->limit(5)
        ->get();

    // FIX: Eager load user name instead of relying on $item->user_id
    $cashierLabels = $cashierPerformance->map(function ($item) {
        $user = User::find($item->cashier_id);
        // FIX: Use cashier_id for lookup and display the user's name
        return $user ? $user->name : "Cashier ID: {$item->cashier_id}"; 
    })->toArray();
    $cashierData = $cashierPerformance->map(fn ($item) => $item->total_sales)->toArray();
    
    // Final Dashboard Return
    return view('admin.dashboard', compact(
        'availableCakesCount', 'availableAddOnsCount', 'salesToday', 'totalOrdersToday',
        'activeCashiers', 'lowStockCount', 'lowStockProducts',
        'cakeLabels', 'cakeData',
        'addonLabels', 'addonData', 
        // Filtered Chart Data:
        'revenueLabels', 'revenueData', 'bestSellerLabels', 'bestSellerData',
        'cashierLabels', 'cashierData', 'transactionData', // transactionData added
        // Filter State Variables (Used by the form for pre-filling)
        'selectedStartDate', 'selectedEndDate', 'selectedInterval',
        'startDate', 'endDate', 'interval' // Keep these for query debugging if needed
    ));
}
    
    /**
     * Display the Stock Inventory Report/Monitoring page.
     * Mapped to the route: admin.reports.inventory-stock
     */
    public function inventoryStock(Request $request): View
    {
        $search = $request->input('search');
        $filter = $request->input('filter');
        $category = $request->input('category');
        
        $query = Product::query();

       

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($filter === 'cakes') {
            $query->whereNotIn('category', [
                'Accessory', 'Beverage', 'Cake Topper (Wedding)', 
                'Candles (Regular)', 'Greeting Card (Birthday)'
            ]);
        }

        if ($filter === 'addons') {
            $query->whereIn('category', [
                'Accessory', 'Beverage', 'Cake Topper (Wedding)', 
                'Candles (Regular)', 'Greeting Card (Birthday)' 
            ]);
        }

        if ($category) {
            $query->where('category', $category);
        }
        
        // --- Fetch Data for Blade View ---
        
        // 1. Paginate the main product list (Used for the main table)
        // This is the variable your blade file was looking for to paginate.
        $productsPaginated = $query
            ->orderBy('stock_quantity', 'asc')
            ->paginate(self::PRODUCTS_PER_PAGE)
            ->withQueryString();

        // 2. Low Stock Products (Separate query for the alert box)
        $lowStockProducts = Product::where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)
                                ->orderBy('stock_quantity', 'asc')
                                ->get();
                                
        // 3. Low Stock Threshold (Constant)
        $lowStockThreshold = self::LOW_STOCK_THRESHOLD;

        // Final Stock Monitoring Return
        return view('admin.reports.stock-monitoring', compact(
            'productsPaginated',
            'lowStockProducts',
            'lowStockThreshold',
            'search'
        ));
    }

/**
 * Helper to fetch product data for inventory reports.
 * NOTE: Assumes you have the Product model imported (use App\Models\Product;).
 */
private function getInventoryStockData(): array
{
    $search = $request->input('search');
    $filter = $request->input('filter');
    $category = $request->input('category');
    
    $query = Product::query();

    // The existing search filter block:
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('sku', 'like', "%$search%")
              ->orWhere('category', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%");
        });
    }

    // Fetch all products, ordered by stock quantity (ascending)
    $allProducts = Product::orderBy('stock_quantity', 'asc')->get();

    // 1. Low Stock Products (for the alert table)
    $lowStockProducts = $allProducts->filter(function ($product) {
        return $product->stock_quantity <= self::LOW_STOCK_THRESHOLD;
    });

    // 2. Full Product Summary (for the main list)
    $uniqueProductSummary = $allProducts; 

    return [
        'lowStockProducts' => $lowStockProducts,
        'uniqueProductSummary' => $uniqueProductSummary,
        
    ];
}

protected function getFormatter(): DecimalMoneyFormatter
{
    return new DecimalMoneyFormatter(new ISOCurrencies());
}

public function showOrderDetails(Order $order): View
{
    $currency = new Currency('PHP');
    $formatter = $this->getFormatter();
    
    $order->load(['items.product', 'cashier']);
    
    $processedTime = $order->created_at->diffForHumans(Carbon::now(), true);
    
    // --- 1. PRECISE ITEM-LEVEL RECALCULATION ---
    $recalculatedItems = $order->items->map(function ($item) use ($currency, $formatter) {
        
        // a. Start with Gross Unit Price (stored in order_items table as $item->price)
        $grossPriceUnitMoney = new Money((int) round($item->price * 100), $currency);
        $quantity = $item->quantity;
        
        // b. Reverse calculate Net Unit Price and Unit VAT
        // Uses the same allocation logic as your DiscountService (100 net parts, 12 VAT parts)
        $allocatedAmounts = $grossPriceUnitMoney->allocate([100, 12]);
        
        $unitPriceNetMoney = $allocatedAmounts[0];
        $unitVatAmountMoney = $allocatedAmounts[1];
        
        // c. Calculate Line Totals based on Quantity
        $linePriceNetMoney = $unitPriceNetMoney->multiply($quantity);
        $lineVatAmountMoney = $unitVatAmountMoney->multiply($quantity);
        $lineGrossTotalMoney = $linePriceNetMoney->add($lineVatAmountMoney);

        return [
            'product_name' => $item->product_name ?? $item->product->name ?? 'N/A',
            'quantity' => $quantity,
            'unit_price_net' => $formatter->format($unitPriceNetMoney),
            'unit_vat_amount' => $formatter->format($unitVatAmountMoney),
            'line_gross_total' => $formatter->format($lineGrossTotalMoney),
            
            // Raw Money objects for Totals calculation (Step 2)
            'money_net_total' => $linePriceNetMoney,
            'money_vat_total' => $lineVatAmountMoney,
        ];
    });

    $subtotalNetMoney = new Money(0, $currency); // Sum of all Net Line Totals
    $totalVatMoney = new Money(0, $currency);    // Sum of all VAT Line Totals
    
    foreach ($recalculatedItems as $item) {
        $subtotalNetMoney = $subtotalNetMoney->add($item['money_net_total']);
        $totalVatMoney = $totalVatMoney->add($item['money_vat_total']);
    }
    
    $grandTotalRecalculatedMoney = $subtotalNetMoney->add($totalVatMoney);

    // Format final totals
    $subtotalNetFormatted = $formatter->format($subtotalNetMoney);
    $totalVatFormatted = $formatter->format($totalVatMoney);
    $grandTotalRecalculatedFormatted = $formatter->format($grandTotalRecalculatedMoney);
    
    // --- 3. AUDIT CHECK ---
    // You should audit $grandTotalRecalculatedFormatted against $order->total_amount

    // Calculate time difference for display
    $processedTime = $order->created_at->diffForHumans(Carbon::now(), true);
    
    // Determine if any discount was applied (for the totals section)
    $isDiscounted = $order->discount_amount > 0 || ($order->vatExemptAmount ?? 0) > 0;
    
    // Pass the original database values for discount display (converted to Money format for consistency)
    

    return view('admin.orders.show', compact(
        'order', 
        'processedTime', 
        'recalculatedItems', // NEW item list
        'subtotalNetFormatted', // NEW Net Subtotal
        'totalVatFormatted',    // NEW VAT Total
        'grandTotalRecalculatedFormatted', // NEW Grand Total
        'isDiscounted', // Discount Flag
       
    ));
}



public function adminProcessRefund(Request $request, Order $order): RedirectResponse
{
    if ($order->status === 'refunded') {
        return back()->with('error', 'This order has already been refunded.');
    }
    
    DB::beginTransaction();
    try {

        // 1. Restore Inventory
        foreach ($order->items as $item) {
            // Use lockForUpdate to prevent race conditions during stock increment
            $product = Product::lockForUpdate()->find($item->product_id); 
            if ($product) {
                $product->increment('stock_quantity', $item->quantity);
            }
        }
        
        // 2. Update Order Status
        $order->update([
            'status' => 'refunded',
            'refunded_at' => Carbon::now(),
        ]);

        DB::commit();
        
        // Redirect back to the order detail page with success message
        return redirect()->route('admin.orders.show', $order)
                         ->with('success', 'Order #' . $order->id . ' has been successfully refunded and stock restored.');
                         
    } catch (\Exception $e) {
        DB::rollBack();
        logger()->error('Admin Refund processing failed: ' . $e->getMessage());
        return back()->with('error', 'Refund failed due to a system error. Stock restoration aborted.');
    }
}


}
