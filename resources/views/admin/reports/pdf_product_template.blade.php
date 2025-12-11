<!DOCTYPE html>
<html>
<head>
    <title>Product Sales Report ({{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }})</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; font-size: 10pt; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #D54F8D; padding-bottom: 10px; }
        .header h1 { color: #D54F8D; margin: 0; font-size: 16pt; }
        .report-info { margin-bottom: 20px; font-size: 10pt; }
        .report-info p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #FFF0F5; color: #D54F8D; font-weight: bold; font-size: 9pt; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-box { margin-top: 30px; width: 40%; float: right; border: 1px solid #D54F8D; padding: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Thea's Delight POS - Product Sales Report</h1>
        <p style="font-size: 9pt; color: #555;">Generated on: {{ date('Y-m-d H:i A') }}</p>
    </div>

    <div class="report-info">
        <p><strong>Report Period:</strong> {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th class="text-right">Total Quantity Sold</th>
                <th class="text-right">Total Gross Revenue </th>
            </tr>
        </thead>
        <tbody>
            @php
                $overallQuantity = 0;
                $overallRevenue = 0;
            @endphp

            @forelse ($productSales as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td class="text-right">{{ number_format($product->total_quantity_sold) }}</td>
                    <td class="text-right">P{{ number_format($product->total_gross_revenue, 2) }}</td>
                </tr>
                @php
                    $overallQuantity += $product->total_quantity_sold;
                    $overallRevenue += $product->total_gross_revenue;
                @endphp
            @empty
                <tr>
                    <td colspan="3" class="text-center">No product sales found in the selected date range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    {{-- Summary Totals Box --}}
    <div class="totals-box">
        <h4>Overall Product Summary</h4>
        <div class="totals-row">
            <span>Total Units Sold:</span>
            <span class="text-right">{{ number_format($overallQuantity) }}</span>
        </div>
        <div class="totals-row" style="border-top: 1px solid #D54F8D; padding-top: 5px; font-weight: bold;">
            <span>Total Gross Revenue:</span>
            <span class="text-right">P{{ number_format($overallRevenue, 2) }}</span>
        </div>
    </div>

</body>
</html>