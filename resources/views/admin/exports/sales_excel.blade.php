<table>
    <thead>
        <tr>
            {{-- Row 1: Title. Merged in PHP: A1:H1 (8 columns) --}}
            <th>Thea's Delight POS Detailed Sales Report</th>
            <th></th> <th></th> <th></th> <th></th> <th></th> <th></th> <th></th>
        </tr>
        <tr>
            {{-- Row 2: Period. Merged in PHP: A2:H2 (8 columns) --}}
            <th>Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</th>
            <th></th> <th></th> <th></th> <th></th> <th></th> <th></th> <th></th>
        </tr>
        <tr>
            {{-- Row 3: Column Headers (8 columns total) --}}
            <th>Order ID</th>
            <th>Date/Time</th>
            <th>Cashier Name</th>
            <th>Payment Method</th>
            <th>Grand Total (PHP)</th>
            <th>Total VAT (PHP)</th>
            <th>Total Discount (PHP)</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detailedOrders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
            <td>{{ $order->cashier->name ?? 'N/A' }}</td>
            <td>{{ $order->payment_method }}</td>
            <td>{{ number_format($order->total_amount, 2) }}</td>
            <td>{{ number_format($order->vat_amount ?? 0, 2) }}</td>
            <td>{{ number_format($order->discount_amount ?? 0, 2) }}</td>
            <td>{{ $order->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>