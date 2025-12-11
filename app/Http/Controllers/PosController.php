<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\DiscountService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Money\Money;
use Money\Currency; 
use App\Services\ReceiptPrinterService;


class PosController extends Controller
{
    // ⭐️ FIX: Updated tax rate to 12% (0.12) for PH VAT calculations
    private const CURRENCY_CODE = 'PHP';
    protected $taxRate = 0.12;
    protected DiscountService $discountService;
    protected ReceiptPrinterService $printerService;

    // ⭐️ FIX: Use Dependency Injection ⭐️
    public function __construct(DiscountService $discountService, ReceiptPrinterService $printerService)
    {
        $this->discountService = $discountService;
        $this->printerService = $printerService;
    }

    /**
     * Get daily orders count for the current cashier
     */
    private function getDailyOrders()
    {
        $user = Auth::user();
        if (!$user) return 0;
        
        return Order::where('cashier_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get today's sales total for the current cashier
     */
    private function getSalesToday()
    {
        $user = Auth::user();
        if (!$user) return 0;
        
        return Order::where('cashier_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total_amount') ?? 0;
    }
    

    /**
     * Display the main POS screen with paginated products and the current cart.
     */
   public function index(Request $request): View
{
    $user = Auth::user();

    // 1. Define the names of the products you consider "accessories"
    // This acts as the new non-category-based filter key.
    $accessoryProductNames = [
        'Cake Topper','Candles','Greeting Card'
    ];

    // --- START OF NEW CODE: Fetch Categories ---
    $allMainCategories = Product::where('stock_quantity', '>', 0)
        ->whereNotIn('category', $accessoryProductNames)
        ->distinct()
        ->pluck('category')
        ->sort()
        ->prepend('All Cakes');

    // 2. Fetch Accessory Products (based on name list)
    // This list will be used for the separate "Add Accessories" section in the view.
    $accessoryProducts = Product::whereIn('category', $accessoryProductNames)
        ->where('stock_quantity', '>', 0)
        ->orderBy('category')
        ->get(); // Fetch as a Collection

    // Get the current filter category from the request URL, defaulting to 'All Cakes'
    $selectedCategory = $request->input('category', 'All Cakes');
    
    // Start the query builder for main products
    $productQuery = Product::where('stock_quantity', '>', 0)
        ->whereNotIn('category', $accessoryProductNames)
        ->orderBy('name');
    
    // APPLY THE FILTER IF A SPECIFIC CATEGORY IS SELECTED
    if ($selectedCategory !== 'All Cakes') {
        // This is the line that performs the filtering on the database
        $productQuery->where('category', $selectedCategory);
    }
    
    // Execute the query, paginate the results, and ensure the category filter 
    // is preserved in the pagination links (withQueryString())
    $products = $productQuery->paginate(4)->withQueryString();

    // 4. Cart Calculation Logic (Unchanged but necessary for context)
    $cart = session()->get('cart', []);
    $subTotal = 0.00;
    $vatTax = 0.00;
    $vatExemptAmount = 0.00; 
    $discountAmount = 0.00;
    $finalTotal = 0.00;
    $currentDiscountType = session('current_discount_type', 'none');

    if (!empty($cart)) {
        $calculatedTotals = $this->discountService->calculateTotals($cart, $currentDiscountType);
        $subTotal = $calculatedTotals['subTotal'];
        $vatTax = $calculatedTotals['vatTax'];
        $vatExemptAmount = $calculatedTotals['vatExemptAmount'];
        $discountAmount = $calculatedTotals['discountAmount'];
        $finalTotal = $calculatedTotals['finalTotal'];
    }

    // --- 4. Transaction History Logic (FIX) ---
    $view = $request->query('view', 'main'); 
    $pastTransactions = collect(); // Initialize as an empty collection
    $date = $request->query('date', Carbon::today()->toDateString());
    $timeOfDay = $request->query('time');

    if ($view === 'history') {
        $query = Order::where('cashier_id', $user->id)
                      ->orderBy('created_at', 'desc');

        // Apply Date Filter
        $query->whereDate('created_at', $date);

        // Apply Time Filter
        if ($timeOfDay === 'morning') {
            $query->whereTime('created_at', '>=', '06:00:00')->whereTime('created_at', '<', '12:00:00');
        } elseif ($timeOfDay === 'afternoon') {
            $query->whereTime('created_at', '>=', '12:00:00')->whereTime('created_at', '<', '18:00:00');
        } elseif ($timeOfDay === 'evening') {
            $query->whereTime('created_at', '>=', '18:00:00')->whereTime('created_at', '<', '24:00:00');
        }
        
        // Fetch the results, eager load cashier for name display
        $pastTransactions = $query->with('cashier')->get(); 
    }

    // 5. Daily Metrics (Unchanged)
    $dailyOrders = $this->getDailyOrders();
    $salesToday = $this->getSalesToday();

    // 6. Pass variables to the view
    return view('cashier.pos_main', [
        'products' => $products, // Main products ONLY (Cakes/non-accessories)
        'accessoryProducts' => $accessoryProducts, // Separate list of accessory products
        'cart' => $cart,
        'subTotal' => $subTotal,
        'vatTax' => $vatTax,
        'discountAmount' => $discountAmount,
        'vatExemptAmount' => $vatExemptAmount,
        'finalTotal' => $finalTotal,
        'dailyOrders' => $dailyOrders,
        'salesToday' => $salesToday,
        'categories' => $allMainCategories,

    'view' => $view, // Ito ang susi!
        'pastTransactions' => $pastTransactions,
        'date' => $date, 
        'timeOfDay' => $timeOfDay,
    ]);
}
    /**
     * Add a product to the cart or increment its quantity.
     */
    public function addItem(Request $request, Product $product): RedirectResponse
    {
       $cart = session('cart', []);
        $productId = $product->id;
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $productId,
                'name' => $product->name,
                'price' => $product->price, // Storing GROSS price
                'quantity' => 1,
                'stock_quantity' => $product->stock_quantity,
                'total' => $product->price,
            ];
        }
        if ($cart[$productId]['quantity'] > $product->stock_quantity) {
             return back()->with('error', 'Cannot add more. Stock limit reached for ' . $product->name);
        }
        // Update total (gross)

        $cart[$productId]['total'] = $cart[$productId]['quantity'] * $cart[$productId]['price'];
        session(['cart' => $cart]);
        return back()->with('success', $product->name . ' added to cart.');
    }

    /**
     * Update item quantity or remove item entirely.
     */
    public function updateItem(Request $request, Product $product): RedirectResponse
    {
        $cart = session('cart', []);
        $productId = $product->id;
        $newQuantity = (int)$request->input('quantity');
        if (!isset($cart[$productId])) {
            return back()->with('error', 'Item not found in cart.');
        }

        if ($newQuantity > $product->stock_quantity) {
            return back()->with('error', 'Cannot set quantity above available stock (' . $product->stock_quantity . ')');
        }
        if ($newQuantity > 0) {
            $cart[$productId]['quantity'] = $newQuantity;
            $cart[$productId]['total'] = $newQuantity * $cart[$productId]['price'];
        } else {
            unset($cart[$productId]);
        }
        session(['cart' => $cart]);
        return back()->with('success', 'Cart updated successfully.');
    }
    /**
     * Remove an item entirely from the cart (used when quantity hits zero).
     */
    public function removeItem($itemId): RedirectResponse
    {
        $cart = session()->get('cart', []);
        // Ensure the item ID is an array key for accurate removal
        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);

        }
        // Re-index the array for consistency, although associative arrays by ID are usually better
        session()->put('cart', array_filter($cart));
        return back()->with('success', 'Item removed from cart successfully.');
    }
    /**
     * Stores the selected discount type in the session for cart calculation.
     */
    public function applyDiscount(Request $request): RedirectResponse
    {
        // NOTE: This method relies on the index method and DiscountService to do the heavy lifting.
        $discountType = $request->input('discount_type');
    
        // Ensure the input is one of the valid types
        if (in_array($discountType, ['sc', 'pwd', 'none'])) {
            session()->put('current_discount_type', $discountType);

            $message = $discountType === 'none'
                              ? 'Discount removed.'
                              : strtoupper($discountType) . ' discount applied (20% & VAT Exempt).';

            return back()->with('success', $message);
        }
        return back()->with('error', 'Invalid discount type selected.');

    }

    // --- PAYMENT FLOW INITIATION ---
    public function checkout(Request $request): RedirectResponse
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('pos.main')->with('error', 'The cart is empty. Cannot process checkout.');
        }
        // RE-CALCULATE totals using the service one last time before checkout
        $currentDiscountType = session('current_discount_type', 'none');
        // The service returns strings/decimals, so we ensure the session stores them that way
        $calculatedTotals = $this->discountService->calculateTotals($cart, $currentDiscountType);
        // Store final totals in session for payment views
        session(['payment_totals' => [
            'subTotal' => $calculatedTotals['subTotal'],
            'vatTax' => $calculatedTotals['vatTax'],
            'vatExemptAmount' => $calculatedTotals['vatExemptAmount'],
            'discountAmount' => $calculatedTotals['discountAmount'],
            'finalTotal' => $calculatedTotals['finalTotal']
        ]]);
        return redirect()->route('pos.pay.select');
    }
    public function showPaymentSelection(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('payment_totals')) {
            return redirect()->route('pos.main')->with('error', 'No transaction initialized.');
        }
        $totals = session('payment_totals');
        $cart = session('cart', []);
        return view('cashier.payment.pay-select', compact('totals', 'cart'));

    }
    /**
     * Shows the receipt and actions (like print/email) after a successful transaction.
     */
    public function showReceiptActions(Request $request): View|RedirectResponse
    {
        $receiptDetails = session('receipt_details');
        if (!$receiptDetails) {
            return redirect()->route('pos.main')->with('error', 'No recent transaction details found.');
        }

        return view('cashier.receipt-actions', [
            'receipt' => $receiptDetails
        ]);

    }
    /**
     * Processes a full refund for a completed order, restoring stock.
     */
    public function processRefund(Request $request, Order $order): RedirectResponse
    {
        if ($order->status === 'refunded') {
            return back()->with('error', 'This order has already been refunded.');
        }
        DB::beginTransaction();
        try {

            // 1. Restore Inventory

            foreach ($order->items as $item) {

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
            return redirect()->route('pos.main')->with('success', 'Order #' . $order->id . ' has been successfully refunded and stock restored.');
        } catch (\Exception $e) {

            DB::rollBack();
            logger()->error('Refund processing failed: ' . $e->getMessage());
            return back()->with('error', 'Refund failed due to a system error. Stock restoration aborted.');
        }
    }
    // --- PAYMENT PROCESSING HELPER (Core Logic for Saving Order and Deducting Stock) ---

    // Add this helper function within your PosController class
private function calculateNetTotal(float $grossPrice, int $quantity, float $taxRate): float
{
    // WARNING: This is still standard PHP float math. 
    // This calculation MUST be handled by the Money library 
    // (using centavos and Money::divide) for financial accuracy.
    // For now, we clean it up and use explicit float conversion.
    return (float) ($quantity * ($grossPrice / (1.0 + $taxRate)));
}


/**
 * Core logic for finalizing the order, saving records, and deducting stock.
 */
private function finalizeOrder(array $cart, array $totals, string $paymentMethod, array $paymentDetails = []): RedirectResponse
{
    $cashierId = Auth::id(); 
    
    if (!$cashierId) {
        return redirect()->route('pos.main')->with('error', 'Authentication failed. Please login again.');
    }
    
    // ⭐️ REQUIRED FIX: DEFINING $dbTotals HERE (This is where the missing code belongs) ⭐️
    // Convert totals from formatted strings back to numeric (REQUIRED for DB storage)
    $dbTotals = [
        'total_amount' => (float)$totals['finalTotal'],
        'tax_amount' => (float)$totals['vatTax'],
        'vatExemptAmount' => (float)$totals['vatExemptAmount'],
        'discount_amount' => (float)$totals['discountAmount'],
    ]; // ⬅️ $dbTotals is defined.


    DB::beginTransaction();
    try {
        // 1. Create the main Order record
        $order = Order::create([
            'cashier_id' => $cashierId,
            'customer_id' => null,
            
            // These lines now correctly use the defined $dbTotals (around line 330)
            'total_amount' => $dbTotals['total_amount'], 
            'tax_amount' => $dbTotals['tax_amount'],
            'vatExemptAmount' => $dbTotals['vatExemptAmount'],
            'discount_amount' => $dbTotals['discount_amount'],
            
            'payment_method' => $paymentMethod,
            'status' => 'completed',
        ]);

   

        // 2. Deduct Inventory and Create Order Items
        foreach ($cart as $item) {
            $product = Product::lockForUpdate()->find($item['id']);

            if (!$product || $product->stock_quantity < $item['quantity']) {
                DB::rollBack();
                return redirect()->route('pos.main')->with('error', 'Transaction failed: Insufficient stock for ' . $item['name']);
            }
            
            $product->decrement('stock_quantity', $item['quantity']);

            // Using the helper function that still contains the float math risk (must be replaced by Money logic later)
            $netPriceTotal = $this->calculateNetTotal($item['price'], $item['quantity'], $this->taxRate);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'price' => $item['price'], 
                'quantity' => $item['quantity'],
                'total' => $netPriceTotal, 
            ]);
        }

        DB::commit();

        // 3. Prepare Receipt Details for printing and session
        $receiptDetails = [
            'order_id' => $order->id,
            'receiptNo' => $order->id,
            'items' => $cart,
            'totals' => $totals,
            'payment_method' => $paymentMethod,
            'received' => $paymentDetails['received_amount'] ?? $totals['finalTotal'],
            'change' => $paymentDetails['change'] ?? 0.00,
        ];

        // 4. ATTEMPT THERMAL PRINT IMMEDIATELY
        $printSuccess = $this->printerService->printEscposReceipt($receiptDetails);

        // 5. Clear cart session and payment totals
        session()->forget(['cart', 'payment_totals', 'current_discount_type']);

        // 6. Set receipt details in persistent session for the actions page
        session()->put('receipt_details', $receiptDetails); 

        // 7. Determine redirect message and redirect
        $message = $printSuccess 
            ? 'Transaction completed successfully. Receipt printing initiated.' 
            : 'Transaction completed. WARNING: Failed to connect to thermal printer.';

        return redirect()->route('receipt.actions')->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        logger()->error('POS Finalize Error: ' . $e->getMessage());
        return redirect()->route('pos.main')->with('error', 'Transaction failed due to a system error. Please try again.');
    }
}


    // --- CASH PAYMENT ---
    public function showCashForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('payment_totals')) {
            return redirect()->route('pos.main');
        }
        $totals = session('payment_totals');
        return view('cashier.payment.pay-cash', compact('totals'));
    }

    public function processCash(Request $request): RedirectResponse
    {
        $totals = session('payment_totals');
        if (!$totals) {
            return redirect()->route('pos.main')->with('error', 'Transaction totals missing.');
        }

        // Ensure comparison is numeric, as $totals['grandTotal'] is a string/decimal
        $finalTotalNumeric = (float) $totals['finalTotal'];
        $request->validate([
            'received_amount' => 'required|numeric|min:' . $finalTotalNumeric,
        ]);
        $cart = session('cart', []);
        $receivedAmount = round($request->received_amount, 2);
        $change = round($receivedAmount - $finalTotalNumeric, 2);
        
        return $this->finalizeOrder($cart, $totals, 'Cash', ['received_amount' => $receivedAmount, 'change' => $change]);
    }
    // --- CARD PAYMENT (Simulated API) ---
    public function showCardForm(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('payment_totals')) {
            return redirect()->route('pos.main');
        }
        $totals = session('payment_totals');
        return view('cashier.payment.pay-card', compact('totals'));
    }
    // app/Http/Controllers/PosController.php

public function processCard(Request $request, PaymentService $paymentService): RedirectResponse
{
    // 1. Fetch the current cart data
    $cart = session('cart', []);
    if (empty($cart)) {
        return redirect()->route('pos.main')->with('error', 'Cart is empty. Cannot process payment.');
    }

    // 2. RE-CALCULATE totals using DiscountService (GUARANTEES 'finalTotal' exists)
    $currentDiscountType = session('current_discount_type', 'none');
    
    // Call the service to get fresh totals
    $calculatedTotals = $this->discountService->calculateTotals($cart, $currentDiscountType); 
    $totals = $calculatedTotals; // Use the fresh data

    // 3. Validation and Payment Logic
    // Now, $totals['finalTotal'] is guaranteed to be set by the DiscountService.
    $finalTotalNumeric = (float) $totals['finalTotal']; 

    $request->validate([
        'card_number' => 'required|digits:16',
        'card_type' => 'required|string',
        'card_expiry' => 'required|string|regex:/^\d{2}\/\d{2}$/',
    ]);

    $paymentSuccess = $paymentService->chargeCard([
        'amount' => $finalTotalNumeric,
        'card_number' => $request->card_number,
    ]);
    
    if (!$paymentSuccess) {
        return back()->withErrors(['card_number' => 'Payment processor denied the transaction.'])
                      ->withInput();
    }
    
    // 4. Finalize the order using the freshly calculated totals
    return $this->finalizeOrder($cart, $totals, 'Card (' . $request->card_type . ')');
}

public function showTransactionDetail(Order $order): View|RedirectResponse
{
    // Security check: Only show orders made by the current cashier or an Admin
    if ($order->cashier_id !== Auth::id() && Auth::user()->role !== 'admin') {
        return redirect()->route('pos.main')->with('error', 'Unauthorized access to this transaction.');
    }
    
    // Eager load items for detailed display
    $order->load(['items', 'cashier']);

    return view('cashier.transaction-detail', compact('order'));
}

public function getPastTransactions(Request $request)
{
    $query = Order::where('cashier_id', Auth::id())
                  ->orderBy('created_at', 'desc');

    // Filter by date (defaults to today)
    $date = $request->query('date', Carbon::today()->toDateString());
    $query->whereDate('created_at', $date);

    // Filter by time of day (Morning/Afternoon/Evening)
    $timeOfDay = $request->query('time');
    
    if ($timeOfDay === 'morning') {
        $query->whereTime('created_at', '>=', '06:00:00')->whereTime('created_at', '<', '12:00:00');
    } elseif ($timeOfDay === 'afternoon') {
        $query->whereTime('created_at', '>=', '12:00:00')->whereTime('created_at', '<', '18:00:00');
    } elseif ($timeOfDay === 'evening') {
        $query->whereTime('created_at', '>=', '18:00:00')->whereTime('created_at', '<', '24:00:00');
    }

    // Eager load items for details view on the POS screen and return the results
    return $query->with('items')->get(); 
}

public function showEWalletForm(Request $request): View|RedirectResponse
{
    // Ensure totals are available before showing the payment form
    if (!$request->session()->has('payment_totals')) {
        return redirect()->route('pos.main')->with('error', 'Please select items first.');
    }
    
    // Retrieve the pre-calculated totals from the checkout step
    $totals = session('payment_totals'); 
    
    // Allows the user to select or defaults to a common type
    $selectedWallet = $request->input('wallet_type', 'GCash'); 
    
    // Make sure you have this view file: resources/views/cashier/payment/pay-e-wallet.blade.php
    return view('cashier.payment.pay-e-wallet', compact('totals', 'selectedWallet'));
}

public function processEWallet(Request $request): RedirectResponse
{
    // 1. Fetch the current cart data
    $cart = session('cart', []);
    if (empty($cart)) {
        return redirect()->route('pos.main')->with('error', 'Cart is empty. Cannot process payment.');
    }

    // 2. RE-CALCULATE totals using DiscountService (Safeguard)
    $currentDiscountType = session('current_discount_type', 'none');
    
    // Call the service to get fresh totals
    $calculatedTotals = $this->discountService->calculateTotals($cart, $currentDiscountType); 
    $totals = $calculatedTotals; // Use the fresh data

    // 3. Validation
    $request->validate(['e_wallet_type' => 'required|string']);
    
    // 4. Finalize the order using the freshly calculated totals
    // No external payment gateway logic is simulated here, so we proceed directly to finalization.
    $paymentMethod = $request->e_wallet_type . ' E-Wallet';

    // Note: We pass an empty array for paymentDetails since no change or received amount is tracked here
    return $this->finalizeOrder($cart, $totals, $paymentMethod, []); 
}

}