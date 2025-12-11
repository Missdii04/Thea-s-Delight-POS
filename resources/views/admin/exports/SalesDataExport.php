<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesDataExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Define the data fetching logic for the Sales Report.
     */
    public function view(): View
    {
        $detailedOrders = Order::query()
            ->with(['cashier'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.exports.sales_excel', [
            'detailedOrders' => $detailedOrders,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    /**
     * Apply styles (colors, bold headers) to the spreadsheet.
     */
    public function styles(Worksheet $sheet)
    {
        // 1. Merge and Style the Report Title (Row 1)
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        // 2. Style the Period (Row 2)
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);

        // 3. Style the Header Row (Row 3, where the table headers start)
        $sheet->getStyle('A3:H3')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('D54F8D')); // Magenta text
        $sheet->getStyle('A3:H3')->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFF0F5'); // Soft Pink background
    }
}