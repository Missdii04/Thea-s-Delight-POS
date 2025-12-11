<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; 
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product; 
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\SalesDataExport;
use App\Exports\ProductDataExport;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Fetches all aggregated data for reporting charts (Day, Week, Month).
     */
    public function index()
    {
        // --- 1. KPI Calculation (Unchanged) ---
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $totalOrders = Order::where('status', 'completed')->count();
        $averageOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

        // --- 2. Full Sales Aggregation (D/W/M) ---
        $salesData = $this->getSalesData();

        // --- 3. Best Seller Aggregation (D/W/M) ---
        // Note: Assumes 'order_items' table is correctly linked to 'products' table.
        $bestSellerData = $this->getBestSellerData();
        
        // --- 4. Prepare Initial Chart Data (Default to Daily) ---
        $chartJsData = $this->formatChartData($salesData['daily'], 'Sales');
        $bestSellerChartData = $this->formatBestSellerData($bestSellerData['daily']);


        $metrics = [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'averageOrderValue' => $averageOrderValue,
        ];

        return view('admin.reports.index', compact(
            'metrics', 
            'chartJsData', // Initial sales chart data
            'bestSellerChartData', // Initial best seller chart data
            'salesData', // Full set of sales data for JS switching
            'bestSellerData' // Full set of best seller data for JS switching
        )); 
    }
    
    /**
     * Display detailed sales data for export, with date filtering.
     */
    public function showSalesData(Request $request): View
    {
        // 1. Get Filters
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now();
        
        // 2. Fetch Detailed Order Data (for the table)
        // Fetch ALL completed orders with necessary details (items and cashier)
        $detailedOrders = Order::query()
            ->with(['items', 'cashier'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // 3. Prepare Charts Data (optional, but good for context)
        // Example: Total transactions count for the period
        $totalTransactions = $detailedOrders->count();

        return view('admin.reports.sales_data_detail', compact(
            'detailedOrders', 
            'startDate', 
            'endDate',
            'totalTransactions'
        ));
    }

    /**
     * Export the detailed sales data to CSV or PDF.
     */
   public function exportSalesData(Request $request, string $format)
    {
        // --- 1. Date Fetching Logic (Identical to showSalesData) ---
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = Carbon::parse($endDateInput)->endOfDay();
        } else {
            // Use defaults (e.g., last 30 days) if no dates are provided in the URL
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subMonth()->startOfDay(); 
        }

        $filename = "sales_report_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.{$format}";

        // --- NEW XLSX Export Logic ---
        if ($format === 'xlsx') {
            return Excel::download(new SalesDataExport($startDate, $endDate), $filename);
        }

        // --- CSV Export Logic (Removed as per request) ---

        // --- PDF Export Logic ---
        if ($format === 'pdf') {
            // 2. Fetch Data for PDF (must be done here, as the export class handles XLSX data)
            $detailedOrders = Order::query()
                ->with(['items', 'cashier'])
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
                
            // 3. Load PDF View and Download
            $pdf = Pdf::loadView('admin.reports.pdf_template', compact('detailedOrders', 'startDate', 'endDate'));
            
            // Use download() to force file download or stream() for new tab view
            // Assuming you want the stream() behavior for printing/viewing in a new tab:
            return $pdf->stream($filename);
        }
        
        return back()->with('error', 'Invalid export format.');
    }

    public function exportProductData(Request $request, string $format)
    {
        // --- 1. Date Fetching Logic (CRITICAL: Needs to be defined here too) ---
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        
        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = Carbon::parse($endDateInput)->endOfDay();
        } else {
            // Use defaults (e.g., last 30 days) if no dates are provided in the URL
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subMonth()->startOfDay(); 
        }

        $filename = "product_sales_report_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.{$format}";

        if ($format === 'xlsx') {
            // CRITICAL: Use the ProductDataExport class here
            return Excel::download(new ProductDataExport($startDate, $endDate), $filename);
        }

        // --- PDF Export Logic ---
        if ($format === 'pdf') {
             // 2. Fetch Data for PDF (must be done here)
            $productSales = OrderItem::query()
                ->select(
                    'product_name',
                    DB::raw('SUM(quantity) as total_quantity_sold'),
                    DB::raw('SUM(quantity * price) as total_gross_revenue') 
                )
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->groupBy('product_name')
                ->orderBy('total_quantity_sold', 'desc')
                ->get();

            // 3. Load PDF View and Download
            // Assumes you created pdf_product_template.blade.php
            $pdf = Pdf::loadView('admin.reports.pdf_product_template', compact('productSales', 'startDate', 'endDate'));
            
            // Use stream() for new tab view/printing
            return $pdf->stream($filename);
        }
        
        return back()->with('error', 'Invalid export format.');
    }


    public function showProductData(Request $request): View
    {
        // 1. Get Filters
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now();
        
        // 2. Fetch Aggregated Item Data (Grouped by Product Name)
        $productSales = OrderItem::query()
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_quantity_sold'),
                // Recalculate total gross revenue derived from this product's sales
                DB::raw('SUM(quantity * price) as total_gross_revenue') 
            )
            // Join Orders table to filter by date and status
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('product_name')
            ->orderBy('total_quantity_sold', 'desc')
            ->get();
        
        // Count unique products sold
        $uniqueProducts = $productSales->count();

        return view('admin.reports.product_data_detail', compact(
            'productSales', 
            'startDate', 
            'endDate',
            'uniqueProducts'
        ));
    }


    /**
     * Export the detailed product sales data to CSV or PDF.
     */


    public function showTabbedReports(Request $request): View
    {
        // 1. Determine the active tab (default to 'sales')
        $activeTab = $request->input('tab', 'sales');
        
        // 2. Determine Date Filters
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->subMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now();
        
        $data = compact('startDate', 'endDate', 'activeTab');
        
        // 3. Fetch Data based on the Active Tab
        if ($activeTab === 'sales') {
            // Fetch Sales Data for the active date range
            $detailedOrders = Order::query()
                ->with(['items', 'cashier'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();
            $data['detailedOrders'] = $detailedOrders;
            $data['totalTransactions'] = $detailedOrders->count();

        } elseif ($activeTab === 'product') {
            // Fetch Product Data (Aggregated) for the active date range
            $productSales = OrderItem::query()
                ->select(
                    'product_name',
                    DB::raw('SUM(quantity) as total_quantity_sold'),
                    DB::raw('SUM(quantity * price) as total_gross_revenue') 
                )
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->groupBy('product_name')
                ->orderBy('total_quantity_sold', 'desc')
                ->get();

            $data['productSales'] = $productSales;
            $data['uniqueProducts'] = $productSales->count();
        }
        
        // 4. Return the master tabbed view (assuming it's named index.blade.php)
        return view('admin.reports.index', $data);
    }




  
    // --- Aggregation Helper Methods ---

    /**
     * Aggregates sales revenue over Day, Week, and Month periods.
     */
    private function getSalesData(): array
    {
        // Daily: Last 7 days
        $daily = Order::select(
                DB::raw('DATE(created_at) as period'),
                DB::raw('SUM(total_amount) as amount')
            )
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();
            
        // Weekly: Last 4 weeks
        $weekly = Order::select(
                DB::raw('WEEK(created_at, 1) as period'),
                DB::raw('SUM(total_amount) as amount')
            )
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', Carbon::now()->subWeeks(4))
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();
            
        // Monthly: Last 12 months
        $monthly = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as period'),
                DB::raw('SUM(total_amount) as amount')
            )
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'period')
            ->orderBy('year', 'asc')
            ->orderBy('period', 'asc')
            ->get();

        return [
            'daily' => $daily,
            'weekly' => $weekly,
            'monthly' => $monthly,
        ];
    }
    
    /**
     * Aggregates best-selling products by quantity over Day, Week, and Month periods.
     * NOTE: Requires an 'order_items' table with product_id and quantity.
     */
    private function getBestSellerData(): array
    {
        $baseQuery = DB::table('order_items')
                        ->join('orders', 'orders.id', '=', 'order_items.order_id')
                        ->join('products', 'products.id', '=', 'order_items.product_id')
                        ->where('orders.status', 'completed')
                        ->select('products.name as name', DB::raw('SUM(order_items.quantity) as quantity'));

        $bestSellersDaily = (clone $baseQuery)
            ->whereDate('orders.created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('products.name')
            ->orderBy('quantity', 'desc')
            ->take(5)
            ->get();

        $bestSellersWeekly = (clone $baseQuery)
            ->whereDate('orders.created_at', '>=', Carbon::now()->subWeeks(4))
            ->groupBy('products.name')
            ->orderBy('quantity', 'desc')
            ->take(5)
            ->get();

        $bestSellersMonthly = (clone $baseQuery)
            ->whereDate('orders.created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('products.name')
            ->orderBy('quantity', 'desc')
            ->take(5)
            ->get();

        return [
            'daily' => $bestSellersDaily,
            'weekly' => $bestSellersWeekly,
            'monthly' => $bestSellersMonthly,
        ];
    }
    
    /**
     * Formats aggregated sales data into Chart.js structure.
     */
    private function formatChartData(\Illuminate\Support\Collection $data, string $labelPrefix): array
    {
        $labels = $data->pluck('period')->map(function ($period) {
            // Attempt to format dates nicely, falling back on the period value
            try {
                return Carbon::parse($period)->format('M d'); 
            } catch (\Exception $e) {
                return $period;
            }
        })->toArray();

        $chartData = $data->pluck('amount')->map(fn($value) => (float)$value)->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    "label" => "{$labelPrefix} Revenue ($)",
                    'backgroundColor' => "rgba(213, 79, 141, 0.4)", // Magenta theme color
                    'borderColor' => "rgba(213, 79, 141, 1)",
                    'data' => $chartData,
                ]
            ]
        ];
    }

    /**
     * Formats best seller data into Chart.js structure (for a pie/bar chart).
     */
    private function formatBestSellerData(\Illuminate\Support\Collection $data): array
    {
        $labels = $data->pluck('name')->toArray();
        $chartData = $data->pluck('quantity')->map(fn($value) => (int)$value)->toArray();
        
        // Define colors for the chart
        $colors = ['#D54F8D', '#F8A0CA', '#B63E78', '#FDE6F1', '#A05C7C'];

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    "label" => "Units Sold",
                    'backgroundColor' => array_slice($colors, 0, count($labels)),
                    'data' => $chartData,
                ]
            ]
        ];
    }
}