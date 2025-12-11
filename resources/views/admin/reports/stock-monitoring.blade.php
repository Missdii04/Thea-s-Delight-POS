@php use Carbon\Carbon; @endphp

<style>
/* Custom Font for a warm feel, Figtree */
@import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap');

/* Style definitions kept for theme consistency */
.bg-soft-pink { background-color: #FFF0F5; }
.text-magenta { color: #D54F8D; }
.bg-magenta { background-color: #D54F8D; }
.hover:bg-pink-700:hover { background-color: #C3417E; }
.border-magenta { border-color: #D54F8D; }
.text-header { color: #D54F8D; }

/* Custom highlight for products below threshold (Must match AdminController's threshold 5) /
.critical-stock {
background-color: #FEE2E2; / Tailwind red-100 */

</style>

<x-app-layout>
<x-slot name="header">
<h2 class="font-extrabold text-2xl text-header leading-tight">
{{ __('Stock Monitoring & Reorder') }}
</h2>
</x-slot>

<div class="py-12 bg-soft-pink">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- Success Message Display (Themed) --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-sm font-semibold text-magenta bg-pink-100 rounded-xl shadow-md border border-magenta">
                {{ session('success') }}
            </div>
        @endif

        {{-- 1. LOW STOCK INVENTORY ALERT TABLE --}}
        <div class="bg-white shadow-2xl sm:rounded-lg rounded-xl p-6 mb-8">
            
            <h3 class="text-2xl font-extrabold mb-4 border-b pb-2 text-red-700">📉 Low Stock Inventory (Alert List)</h3>

            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm font-semibold text-red-800">
                    The following {{ $lowStockProducts->count() }} products are currently at or below the critical threshold of {{ $lowStockThreshold }} units. Immediate action is recommended.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 responsive-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Update Stock</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Loop through ONLY the Low Stock Products --}}
                        @forelse ($lowStockProducts as $product)
                            <tr class="critical-stock">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-800" data-label="Product Name">
                                    {{ $product->name }} (SKU: {{ $product->sku }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Category">
                                    {{ $product->category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-600" data-label="Current Stock">
                                    {{ $product->stock_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm" data-label="Update Stock">
                                    <form method="POST" action="{{ route('admin.products.adjust_stock', $product) }}" class="flex items-center space-x-2 justify-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="new_stock" value="{{ $product->stock_quantity }}" min="0" 
                                                class="w-20 border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-magenta focus:ring-magenta" required>
                                        <button type="submit" class="px-3 py-1 bg-magenta text-white rounded-lg text-xs hover:bg-pink-700 transition">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 bg-pink-50">
                                    All products are currently above the minimum stock threshold ({{ $lowStockThreshold }} units).
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Success Message Display (Themed) --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-sm font-semibold text-magenta bg-pink-100 rounded-xl shadow-md border border-magenta">
                {{ session('success') }}
            </div>
        @endif

        {{-- NEW: SEARCH BAR FORM --}}
        <form method="GET" action="{{ route('admin.reports.inventory-stock') }}" class="mb-6">
            <div class="flex items-center space-x-2">
                <input type="text" name="search" placeholder="Search product name, SKU, or category..."
                    value="{{ old('search', $search ?? '') }}"
                    class="flex-1 border-gray-300 rounded-md shadow-sm p-2.5 focus:border-magenta focus:ring-magenta">
                
                <button type="submit" class="px-4 py-2 bg-magenta text-white rounded-md text-sm hover:bg-pink-700 transition">
                    Search
                </button>
                
                {{-- Optional: Clear Search Button (Visible only if a search term is active) --}}
                @if ($search)
                    <a href="{{ route('admin.reports.inventory-stock') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 transition">
                        Clear Search
                    </a>
                @endif
            </div>
        </form>

        {{-- 2. FULL INVENTORY LIST (Replaced Chart/Category Summary) --}}
        <div class="bg-white shadow-2xl sm:rounded-lg rounded-xl p-6">
            
            <h3 class="text-2xl font-extrabold mb-4 border-b pb-2 text-header">📦 Full Inventory List (All Products)</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-pink-100">
                    <thead class="bg-soft-pink">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-extrabold text-magenta uppercase tracking-wider">Product Name (SKU)</th>
                            <th class="px-6 py-3 text-left text-xs font-extrabold text-magenta uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-extrabold text-magenta uppercase tracking-wider">Current Stock</th>
                            <th class="px-6 py-3 text-center text-xs font-extrabold text-magenta uppercase tracking-wider">Update Stock</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-pink-50">
                        {{-- FIX: Loop through $uniqueProductSummary (All Products by Name/SKU) --}}
                        @forelse ($productsPaginated as $product)
                            @php
                                $isLowStock = $product->stock_quantity <= $lowStockThreshold;
                            @endphp
                            <tr class="{{ $isLowStock ? 'critical-stock' : '' }}">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium {{ $isLowStock ? 'text-red-800' : 'text-gray-900' }}">
                                    {{ $product->name }} ({{ $product->sku }})
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $product->category }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-right font-bold {{ $isLowStock ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($product->stock_quantity) }} units
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-center text-sm">
                                    <form method="POST" action="{{ route('admin.products.adjust_stock', $product) }}" class="flex items-center space-x-2 justify-center">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="new_stock" value="{{ $product->stock_quantity }}" min="0" 
                                                class="w-20 border-gray-300 rounded-md shadow-sm text-sm p-1.5 focus:border-magenta focus:ring-magenta" required>
                                        <button type="submit" class="px-3 py-1 bg-magenta text-white rounded-lg text-xs hover:bg-pink-700 transition">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 bg-pink-50">
                                    No products found in inventory.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                 {{-- PAGINATION (KEEPS FILTER) --}}
            <div class="mt-4">
               {{ $productsPaginated->appends(request()->query())->links() }}
            </div>
            </div>
        </div>
       
    </div>
</div>
 


</x-app-layout>