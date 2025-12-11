<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AdminController;
use App\Models\Product;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\RegisterPinController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\OtpChallengeController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReceiptController;

use App\Enums\ProductCategory;




/*
|--------------------------------------------------------------------------
| PUBLIC & SECURITY ROUTES (Auth Flow)
|--------------------------------------------------------------------------
*/

// root landing page 
Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    // 1. Show Registration Form
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    // 2. Process Registration -> Redirects to PIN Challenge
    Route::post('register', [RegisteredUserController::class, 'store']); 
    
    // --- PIN VERIFICATION FLOW ROUTES ---
    // 3. Show PIN Entry Form (THE MISSING ROUTE)
    Route::get('/register/pin-challenge', [RegisterPinController::class, 'showPinForm'])->name('register.pin.show');
    // 4. Verify PIN Submission 
    Route::post('/register/pin-verify', [RegisterPinController::class, 'verifyPin'])->name('register.pin.verify');
    // 5. Success Page (Optional)
    Route::get('/register/success', [RegisterPinController::class, 'showSuccess'])->name('register.success');

   

    require __DIR__.'/auth.php';
}
);

 Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

    /*
|--------------------------------------------------------------------------
| 2. CORE AUTHENTICATED ROUTES (OTP & Dashboard)
|--------------------------------------------------------------------------
*/
    // OTP Challenge for Login (Should be here as it applies to unauthenticated users)
   Route::middleware(['auth'])->group(function () {

    // --- 2FA LOGIN CHALLENGE ROUTES ---
Route::get('/otp/challenge', [OtpChallengeController::class, 'show'])->name('otp.challenge');
Route::post('/otp/verify', [OtpChallengeController::class, 'verifyPin'])->name('otp.verify');
// FIX: Changed 'resend' to 'resendPin' to match the method name in OtpChallengeController
Route::post('/otp/resend', [OtpChallengeController::class, 'resendPin'])->name('otp.resend');


// --- REGISTRATION PIN VERIFICATION ROUTES ---
// CRITICAL ADDITION: Route to show the registration PIN form (called from RegisteredUserController)
Route::get('/register/pin', [RegisterPinController::class, 'showPinForm'])->name('register.pin.show');
// CRITICAL ADDITION: Route to handle the PIN submission for registration
Route::post('/register/pin/verify', [RegisterPinController::class, 'verifyPin'])->name('register.pin.verify');
// Your existing resend route (path updated for consistency, but the old path will also work)
Route::post('/register/pin-resend', [RegisterPinController::class, 'resendPin'])->name('register.pin.resend');

    // --- Verified Routes (General Access) ---
    Route::middleware(['verified'])->group(function () {
        // Admin Dashboard (Primary destination for Admin)
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Standard User Profile Routes (Accessed by all roles, unless prefixed/customized)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    });

/*
|--------------------------------------------------------------------------
| 3. ADMIN ROUTES (Protected by 'role:admin')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard (Redefined to use the admin prefix/name)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard'); 
    
    // 2. Product Management (CRUD & Stock Adjustment)
    Route::resource('products', ProductController::class)->names('products');
    Route::put('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust_stock');

    // 3. User Management (CRUD & Status)
    Route::resource('users', UserController::class)->names('users');
    Route::put('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::put('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate'); 

    // 4. Reporting & Inventory
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    // Renamed the route name to match Tailwind's utility class convention for clarity
    Route::get('/reports/inventory-stock', [AdminController::class, 'inventoryStock'])->name('reports.inventory-stock'); 
    
    
    // 5. Admin POS Test Screen
    Route::get('/pos', function () {
        $products = Product::select('id', 'name', 'price')->get(); 
        return view('admin.pos_test', compact('products'));
    })->name('pos.test');
    
    // 6. Admin Profile Management (Using separate routes for admin area profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Order/Refund Management ---
// 1. Route for viewing the Purchased Detail page (The 'Review/Refund' link)
Route::get('/orders/{order}', [AdminController::class, 'showOrderDetails'])->name('orders.show');

// 2. Route for processing the actual refund
Route::post('/orders/{order}/refund', [AdminController::class, 'adminProcessRefund'])->name('orders.refund');

// --- NEW SALES DATA ROUTE ---
Route::get('/reports/sales-data', [ReportController::class, 'showSalesData'])->name('reports.sales.data');

// --- NEW EXPORT ROUTES ---
// --- NEW EXPORT ROUTES ---
Route::get('/reports/sales-data/export/{format}', [ReportController::class, 'exportSalesData'])
    ->name('reports.sales.export');

// --- NEW PRODUCT DATA ROUTES ---
Route::get('/reports/product-data/export/{format}', [ReportController::class, 'exportProductData'])
    ->name('reports.product.export');
    Route::get('/reports', [ReportController::class, 'showTabbedReports'])->name('reports.index');
});


/*
|--------------------------------------------------------------------------
| OPERATIONAL POS ROUTES (Protected by 'role:admin,cashier')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin,cashier'])->group(function () {
    
    Route::get('/pos/main', [PosController::class, 'index'])
    ->name('pos.main');

    
    
    // POS Cart Actions
    // It requires the DELETE HTTP method and accepts the item ID as a parameter.
    Route::delete('/remove-item/{itemId}', [PosController::class, 'removeItem'])->name('remove_item');
    Route::post('/pos/add-item/{product}', [PosController::class, 'addItem'])->name('pos.add_item');
    Route::post('/pos/update-item/{product}', [PosController::class, 'updateItem'])->name('pos.update_item');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout'); // Initiates payment flow

   Route::post('/apply-discount', [PosController::class, 'applyDiscount'])->name('pos.apply_discount');
    
    // ⭐️ PAYMENT SUB-FLOW ROUTES ⭐️
    Route::get('/pos/pay-select', [PosController::class, 'showPaymentSelection'])->name('pos.pay.select');
    Route::get('/pos/pay-cash', [PosController::class, 'showCashForm'])->name('pos.pay.cash');
    Route::post('/pos/pay-cash', [PosController::class, 'processCash'])->name('pos.process.cash');
    Route::get('/pos/pay-card', [PosController::class, 'showCardForm'])->name('pos.pay.card');
    Route::post('/pos/pay-card', [PosController::class, 'processCard'])->name('pos.process.card');
    Route::get('/pos/pay-e-wallet', [PosController::class, 'showEWalletForm'])->name('pos.pay.ewallet');
    Route::post('/pos/pay-e-wallet', [PosController::class, 'processEWallet'])->name('pos.process.ewallet');
    
    // Transaction History & Refund Review
    Route::get('/pos/transactions/history', [PosController::class, 'getPastTransactions'])
    ->name('pos.transactions.history');
    Route::get('/pos/transaction/{order}', [PosController::class, 'showTransactionDetail'])->name('pos.transaction.detail');
    Route::post('/pos/transaction/{order}/refund', [PosController::class, 'processRefund'])->name('pos.transaction.refund');

    // Cashier Profile Management (Restricted access)
    Route::get('/cashier/profile', [ProfileController::class, 'edit'])->name('cashier.profile.edit');
    Route::patch('/cashier/profile', [ProfileController::class, 'update'])->name('cashier.profile.update');
});

// ⭐️ RECEIPT & OUTPUT ROUTES (CORRECTED) ⭐️
Route::get('/receipt/actions', [ReceiptController::class, 'showActions'])->name('receipt.actions');

// Route to trigger ESC/POS raw printing
Route::get('/receipt/thermal', [ReceiptController::class, 'printThermal'])->name('receipt.print.thermal');

// Route to generate and download the PDF
Route::get('/receipt/pdf', [ReceiptController::class, 'generatePdf'])->name('receipt.generate.pdf');



Route::get('/test-enum', function () {
    echo "<h1>Testing ProductCategory Enum</h1>";
    
    echo "<h3>1. Cake Categories:</h3>";
    echo "<pre>";
    print_r(ProductCategory::getCakeCategories());
    echo "</pre>";
    
    echo "<h3>2. Accessory Categories:</h3>";
    echo "<pre>";
    print_r(ProductCategory::getAccessoryCategories());
    echo "</pre>";
    
    echo "<h3>3. All Categories:</h3>";
    echo "<pre>";
    print_r(ProductCategory::getAllCategories());
    echo "</pre>";
    
    echo "<h3>4. Test with a Product:</h3>";
    $product = Product::first();
    if ($product) {
        echo "Product: " . $product->name . "<br>";
        echo "Category: " . $product->category . "<br>";
        echo "Category Value: " . $product->category->value . "<br>";
        echo "Is Cake?: " . ($product->isCake() ? 'YES' : 'NO') . "<br>";
    } else {
        echo "No products in database.";
    }
});

