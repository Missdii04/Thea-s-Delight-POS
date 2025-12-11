<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
// ⭐️ You must import the ReceiptPrinterService ⭐️
use App\Services\ReceiptPrinterService; 

class ReceiptController extends Controller
{
    // ⭐️ Inject the printer service for thermal printing ⭐️
    public function __construct(protected ReceiptPrinterService $printerService) {}

    /**
     * Display the transaction receipt actions page.
     * This is the main landing page after checkout.
     */
    public function showActions(Request $request): View|RedirectResponse
    {
        // Check if receipt data exists in session
        if (!$request->session()->has('receipt_details')) {
            return redirect()->route('pos.main')->with('error', 'No receipt data found.');
        }

        $receiptDetails = $request->session()->get('receipt_details');

        // Note: The redundant session put has been removed.
        // We assume the data persistence is handled correctly in PosController.

        // Otherwise, show the actions page
        return view('cashier.receipt-actions', compact('receiptDetails'));
    }

    /**
     * Generates and downloads a PDF copy of the receipt.
     */
    public function generatePdf(Request $request): \Illuminate\Http\Response|RedirectResponse
    {
        if (!$request->session()->has('receipt_details')) {
            return redirect()->route('pos.main')->with('error', 'No receipt data found for PDF.');
        }

        // ⭐️ FIX: Get data from session and pass it to the view ⭐️
        $receiptDetails = $request->session()->get('receipt_details');

    $pdf = Pdf::loadView('cashier.receipt', compact('receiptDetails'))
        // ⭐️ FIX: Set paper size to a standard receipt width (80mm) and custom height ⭐️
        ->setPaper([0, 0, 302, 500], 'portrait'); 
        // Note: 302 points ≈ 80mm. The height (600) is large enough to contain the content.

    return $pdf->download('receipt-' . $receiptDetails['receiptNo'] . '.pdf');
}
    
    /**
     * Triggers ESC/POS printing to a thermal printer.
     */
    public function printThermal(Request $request): RedirectResponse
    {
        $receiptDetails = $request->session()->get('receipt_details');

        if (!$receiptDetails) {
            return back()->with('error', 'No receipt data found for thermal printing.');
        }

        if ($this->printerService->printEscposReceipt($receiptDetails)) {
            return back()->with('success', 'Thermal receipt sent to printer.');
        } else {
            return back()->with('error', 'Failed to connect to thermal printer. Check IP/connection.');
        }
    }

    // ❌ REMOVED: public function printReceipt(Request $request) as it is redundant
}