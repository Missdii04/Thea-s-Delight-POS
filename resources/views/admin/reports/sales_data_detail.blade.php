<x-app-layout>
    <style>
        .text-magenta { color: #D54F8D; }
        .bg-magenta { background-color: #D54F8D; }
        .bg-soft-pink { background-color: #FFF0F5; }
    </style>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-magenta leading-tight">
            {{ __('Detailed Sales & Transaction Report') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-soft-pink">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter and Export Form --}}
            <div class="bg-white shadow-xl rounded-xl p-6 mb-6">
                <form method="GET" action="{{ route('admin.reports.sales.data') }}" class="flex flex-wrap items-end space-x-4 mb-4">
                    
                    {{-- Start Date --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" 
                            value="{{ $startDate->format('Y-m-d') }}"
                            class="mt-1 block py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-magenta focus:border-magenta sm:text-sm">
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" 
                            value="{{ $endDate->format('Y-m-d') }}"
                            class="mt-1 block py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-magenta focus:border-magenta sm:text-sm">
                    </div>

                    {{-- Apply Button --}}
                    <button type="submit" class="px-4 py-2 bg-magenta text-white rounded-md text-sm hover:bg-pink-700 transition duration-150">
                        View Report
                    </button>
                </form>

                <div class="flex space-x-3 border-t pt-4 mt-4">
                    <h4 class="text-lg font-bold text-gray-700">Export:</h4>
                    
                    {{-- Export to CSV --}}
                    <a href="{{ route('admin.reports.sales.export', ['format' => 'csv', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                       class="px-3 py-1 bg-green-500 text-white rounded-md text-sm hover:bg-green-600 transition">
                        Download CSV
                    </a>

                    {{-- Export to PDF (Print) --}}
                    <a href="{{ route('admin.reports.sales.export', ['format' => 'pdf', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}"
                       class="px-3 py-1 bg-red-500 text-white rounded-md text-sm hover:bg-red-600 transition" target="_blank">
                        Download/Print PDF
                    </a>
                </div>
            </div>


            {{-- Detailed Data Table --}}
            <div class="bg-white shadow-xl rounded-xl overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-extrabold text-magenta mb-4">Transactions ({{ $totalTransactions }} total)</h3>
                    <p class="text-sm text-gray-600 mb-4">Data from {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-pink-100">
                        <thead class="bg-soft-pink text-magenta uppercase text-xs leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Order ID</th>
                                <th class="py-3 px-6 text-left">Date/Time</th>
                                <th class="py-3 px-6 text-left">Cashier</th>
                                <th class="py-3 px-6 text-left">Method</th>
                                <th class="py-3 px-6 text-right">Grand Total </th>
                                <th class="py-3 px-6 text-right">VAT </th>
                                <th class="py-3 px-6 text-right">Discount</th>
                                <th class="py-3 px-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-pink-50 text-sm">
                            @forelse ($detailedOrders as $order)
                            <tr class="hover:bg-pink-50">
                                <td class="py-3 px-6 text-left">{{ $order->id }}</td>
                                <td class="py-3 px-6 text-left whitespace-nowrap">{{ $order->created_at->format('Y-m-d H:i A') }}</td>
                                <td class="py-3 px-6 text-left">{{ $order->cashier->name ?? 'N/A' }}</td>
                                <td class="py-3 px-6 text-left">{{ $order->payment_method }}</td>
                                <td class="py-3 px-6 text-right font-bold">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-3 px-6 text-right">P{{ number_format($order->tax_amount, 2) }}</td>
                                <td class="py-3 px-6 text-right">P{{ number_format($order->discount_amount, 2) }}</td>
                                <td class="py-3 px-6 text-center">{{ ucfirst($order->status) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-gray-500">No completed orders found in the selected date range.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>