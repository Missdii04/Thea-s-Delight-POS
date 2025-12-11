<?php

namespace App\Exports;

use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductDataExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Define the data fetching logic for the Product Report.
     */
    public function view(): View
    {
        // Debug: Log the dates being used
        \Log::info('ProductDataExport - Fetching data', [
            'startDate' => $this->startDate->format('Y-m-d H:i:s'),
            'endDate' => $this->endDate->format('Y-m-d H:i:s'),
        ]);

        $productSales = OrderItem::query()
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_quantity_sold'),
                DB::raw('SUM(quantity * price) as total_gross_revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$this->startDate, $this->endDate])
            ->groupBy('product_name')
            ->orderBy('total_quantity_sold', 'desc')
            ->get();

        // Debug: Log the results
        \Log::info('ProductDataExport - Data fetched', [
            'count' => $productSales->count(),
            'first_item' => $productSales->first()
        ]);

        return view('admin.exports.product_excel', [
            'productSales' => $productSales,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Merge and Style the Report Title (Row 1)
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // 2. Style the Period (Row 2)
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);

        // 3. Style the Header Row (Row 3)
        $sheet->getStyle('A3:C3')->getFont()->setBold(true);
        $sheet->getStyle('A3:C3')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFF0F5'); // Soft Pink background
              
        // 4. Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
    }
}