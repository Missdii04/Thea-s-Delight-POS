<!DOCTYPE html>
<html>
<head>
    <title>Sales Report ({{ $startDate->format('Y-m-d') }} to {{ $endDate->format('Y-m-d') }})</title>
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
        .totals-box h4 { margin-top: 0; color: #D54F8D; font-size: 12pt; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Thea's Delight POS - Detailed Sales Report</h1>
        <p style="font-size: 9pt; color: #555;">Generated on: {{ date('Y-m-d H:i A') }}</p>
    </div>

    <div class="report-info">
        <p><strong>Report Period:</strong> {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
        <p><strong>Total Transactions:</strong> {{ count($detailedOrders) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date/Time</th>
                <th>Cashier</th>
                <th>Method</th>
                <th class="text-right">Grand Total</th>
                <th class="text-right">VAT </th>
                <th class="text-right">Discount </th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $overallTotal = 0;
                $overallVAT = 0;
                $overallDiscount = 0;
            @endphp

            @forelse ($detailedOrders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $order->cashier->name ?? 'N/A' }}</td>
                    <td>{{ $order->payment_method }}</td>
                    <td class="text-right">{{ number_format($order->total_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($order->tax_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($order->discount_amount, 2) }}</td>
                    <td class="text-center">{{ ucfirst($order->status) }}</td>
                </tr>
                @php
                    $overallTotal += $order->total_amount;
                    $overallVAT += $order->tax_amount;
                    $overallDiscount += $order->discount_amount;
                @endphp
            @empty
                <tr>
                    <td colspan="8" class="text-center">No completed orders found in the selected date range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    {{-- Summary Totals Box --}}
    <div class="totals-box">
        <h4>Summary Totals</h4>
        <div class="totals-row">
            <span>Gross Revenue:</span>
            <span class="text-right">P{{ number_format($overallTotal, 2) }}</span>
        </div>
        <div class="totals-row">
            <span>Total VAT Collected:</span>
            <span class="text-right">P{{ number_format($overallVAT, 2) }}</span>
        </div>
        <div class="totals-row">
            <span>Total Discounts:</span>
            <span class="text-right">P{{ number_format($overallDiscount, 2) }}</span>
        </div>
        <div class="totals-row" style="border-top: 1px solid #D54F8D; padding-top: 5px; font-weight: bold;">
            <span>Net Sales (Excl. VAT):</span>
            <span class="text-right">P{{ number_format($overallTotal - $overallVAT, 2) }}</span>
        </div>
    </div>

</body>
</html>