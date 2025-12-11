<table>
    <thead>
        <tr>
            {{-- Row 1: Title. Merged in PHP: A1:C1 (3 columns) --}}
            <th>Thea's Delight POS Product Sales Report</th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            {{-- Row 2: Period. Merged in PHP: A2:C2 (3 columns) --}}
            <th>Period: {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            {{-- Row 3: Column Headers (3 columns total) --}}
            <th>Product Name</th>
            <th>Total Quantity Sold</th>
            <th>Total Gross Revenue (PHP)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($productSales as $product)
        <tr>
            <td>{{ $product->product_name }}</td>
            <td>{{ $product->total_quantity_sold }}</td>
            <td>₱{{ number_format($product->total_gross_revenue, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" style="text-align: center;">No sales data found for the selected period.</td>
        </tr>
        @endforelse
    </tbody>
</table>