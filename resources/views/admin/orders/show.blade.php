@php 
    use Carbon\Carbon; 
    // All calculation variables are now passed from the controller:
    // $order, $processedTime, $recalculatedItems, $subtotalNetFormatted, etc.
@endphp

<title>Thea's Delight - Transaction Detail</title>
<style>
    /* Theme definitions */
    .text-magenta { color: #D54F8D; }
    .bg-soft-pink { background-color: #FFF0F5; }
    .text-header { color: #D54F8D; }
    .bg-magenta { background-color: #D54F8D; }
    .hover\:bg-pink-700:hover { background-color: #C3417E; }
    .border-magenta { border-color: #D54F8D; }
</style>

<x-app-layout >
    <div class="bg-soft-pink">
    <x-slot name="header" >
        <h2 class="font-semibold text-xl text-header leading-tight">
            {{ __('Transaction Review: #') . $order->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="p-4 mb-4 text-sm font-semibold text-white bg-magenta rounded-lg shadow-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm font-semibold text-red-800 bg-red-100 rounded-lg shadow-md border border-red-300">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="bg-white shadow-2xl rounded-xl p-8 border border-pink-100">
                
                <h1 class="text-3xl font-extrabold mb-6 text-magenta border-b border-pink-300 pb-3">
                    Purchased Detail
                </h1>
                
                <div class="flex justify-between items-start border-b pb-4 mb-4">
                    <div class="text-sm space-y-2">
                        <p><strong class="text-gray-700">Receipt No:</strong> {{ $order->id }}</p>
                        <p><strong class="text-gray-700">Date:</strong> {{ Carbon::parse($order->created_at)->format('Y-m-d H:i A') }}</p>
                        <p><strong class="text-gray-700">Cashier:</strong> {{ $order->cashier->name ?? 'N/A' }}</p>
                        <p><strong class="text-gray-700">Method:</strong> {{ $order->payment_method }}</p>
                    </div>

                    <div class="text-right">
                        {{-- DYNAMIC STATUS DISPLAY --}}
                        <p class="font-bold text-xl 
                            @if ($order->status === 'completed') text-green-600 
                            @elseif ($order->status === 'refunded') text-red-600
                            @else text-yellow-600
                            @endif">
                            STATUS: {{ strtoupper($order->status) }}
                        </p>
                        <p class="text-xs text-gray-500">Processed {{ $processedTime }} ago</p>
                    </div>
                </div>

                {{-- RECALCULATED ITEM LIST TABLE (New Columns) --}}
                <h3 class="font-semibold text-xl mb-3 border-b border-pink-100 pb-2 text-gray-700">Items Sold</h3>
                <div class="overflow-x-auto border rounded-lg mb-6 shadow-inner">
                    <table class="min-w-full text-sm">
                        <thead class="bg-pink-100 text-magenta">
                            <tr>
                                <th class="py-3 px-4 text-left">Product Name</th>
                                <th class="py-3 px-4 text-center">Qty</th>
                                <th class="py-3 px-4 text-right">Unit Price (Net)</th>
                                <th class="py-3 px-4 text-right">Unit VAT</th>
                                <th class="py-3 px-4 text-right">Gross Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-pink-100">
                            @foreach ($recalculatedItems as $item)
                                <tr class="hover:bg-pink-50">
                                    <td class="py-2 px-4 text-left">{{ $item['product_name'] }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item['quantity'] }}</td>
                                    <td class="py-2 px-4 text-right">₱{{ $item['unit_price_net'] }}</td>
                                    <td class="py-2 px-4 text-right">₱{{ $item['unit_vat_amount'] }}</td>
                                    <td class="py-2 px-4 text-right font-bold">₱{{ $item['line_gross_total'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- RECALCULATED TRANSACTION TOTALS (New Logic) --}}
                <div class="max-w-xs ml-auto border p-4 rounded-lg bg-pink-50 shadow-md">
                    <h4 class="font-bold text-magenta mb-2">Transaction Totals</h4>
                    
                    <div class="space-y-1">
                        {{-- Subtotal (Net Price Total) --}}
                        <div class="flex justify-between text-sm">
                            <span>Subtotal (Net):</span> 
                            <span>₱{{ $subtotalNetFormatted }}</span>
                        </div>
                        
                        
                        
                        {{-- VAT (Tax Charged) --}}
                        <div class="flex justify-between text-sm text-red-600">
                            <span>VAT (12%):</span>
                            <span>₱{{ $totalVatFormatted }}</span>
                        </div>
                        
                        {{-- GRAND TOTAL (Recalculated) --}}
                        <div class="flex justify-between font-extrabold text-xl border-t pt-2 text-magenta">
                            <span>GRAND TOTAL:</span>
                            <span>₱{{ $grandTotalRecalculatedFormatted }}</span>
                        </div>
                    </div>
                </div>
                
                {{-- REFUND ACTION AREA --}}
                <div class="mt-8 pt-4 border-t border-pink-300">
                    @if ($order->status === 'completed')

                    <div 
                        x-data="{ showRefundModal: false }"
                        @keydown.escape.window="showRefundModal = false"
                    >

                        <button
                            type="button"
                            @click="showRefundModal = true"
                            class="bg-red-600 text-white py-2 px-4 rounded-lg font-semibold
                                   hover:bg-red-700 transition shadow"
                        >
                            Process Full Refund
                        </button>

                        <div
                            x-show="showRefundModal"
                            x-cloak
                            class="fixed inset-0 z-50 flex items-start justify-center bg-black/60 backdrop-blur-sm"
                        >
                            <div
                                class="absolute inset-0"
                                @click="showRefundModal = false"
                            ></div>

                            <div
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="relative mt-24 w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden"
                            >
                                <div class="flex items-center gap-4 px-6 py-5 bg-red-50 border-b border-red-100">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-extrabold text-red-700">
                                            Confirm Full Refund
                                        </h2>
                                    </div>
                                </div>

                                <div class="px-6 py-4">
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        You are about to process a
                                        <span class="font-bold text-red-600">FULL REFUND</span> Receipt #{{ $order->id }}.
                                    </p>

                                    <p class="mt-3 text-sm font-semibold text-red-600">
                                        ⚠️ This action cannot be undone.
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Inventory will be automatically restored.
                                    </p>
                                </div>

                                <div class="flex justify-end gap-3 px-6 py-2 bg-gray-50 border-t">

                                    <button
                                        type="button"
                                        @click="showRefundModal = false"
                                        class="text-gray-700"
                                    >
                                        Cancel
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.orders.refund', $order) }}"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-5 py-2 rounded-md bg-red-600 text-white
                                                   font-semibold hover:bg-red-700 transition shadow"
                                        >
                                            Confirm Refund
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                    @else
                    {{-- Status is REFUNDED or CANCELLED --}}
                    <p class="text-sm text-gray-600">
                        Refund not available. Status:
                        <strong class="text-red-700">{{ strtoupper($order->status) }}</strong>
                    </p>
                    @endif

                    @php
    // CRITICAL FIX: Pull all query parameters directly from the current URL
    // This includes: cashier_id, start_date, end_date, report_interval
    $filters = request()->query();
    
    // We use array_filter to remove any empty or null values, but keep the valid ones
    $cleanFilters = array_filter($filters);
            @endphp

            {{-- Back Button --}}
            <a href="{{ route('admin.users.index', $cleanFilters) }}"
            class="mt-4 block text-sm text-pink-600 hover:text-pink-700 transition">
                ← Back to Transactions List
            </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>