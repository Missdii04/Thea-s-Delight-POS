@php use Carbon\Carbon; @endphp
<title>Thea's Delight - POS</title>
<style>
    /* Custom Font for a warm feel, Figtree */
    @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap');
    body {
        font-family: 'Figtree', sans-serif;
        /* Pastel Pink Background */
        background-color: #FADDE6;
    }
    /* Custom accent color: Magenta */
    .bg-magenta { background-color: #D54F8D; } /* Deep Magenta for accents/buttons */
    .text-magenta { color: #D54F8D; }
    .hover\:bg-pink-700:hover { background-color: #C3417E; }
    .border-magenta { border-color: #D54F8D; }
    /* SUPER SOFT PINK: Almost white, used for contrast behind white containers */
    .bg-super-soft-pink { background-color: #f5b5d5ff; }
    .bg-soft-pink { background-color: #e2a8bcff; }
    .text-header { color: #D54F8D; }
    .product-card-hover:hover {
        border-color: #D54F8D;
        transform: translateY(-2px) scale(1.02);
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-header leading-tight">
            {{ __('Sweet Treats POS Interface') }}
        </h2>
    </x-slot>

    <div class="py-4 bg-super-soft-pink">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="p-4 mb-4 text-sm font-semibold text-white bg-green-500 rounded-xl shadow-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 mb-4 text-sm font-semibold text-white bg-red-500 rounded-xl shadow-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-wrap -mx-3" x-data="{ activeTab: 'new' }">
    <div class="w-full lg:w-3/4 px-3 mb-6 lg:mb-0">
        <div class="bg-white shadow-2xl rounded-xl p-4 h-[90vh] flex flex-col">
            <div class="flex border-b border-pink-400 mb-4 flex-shrink-0">
                <button @click="activeTab = 'new'"
                    :class="{'border-magenta text-magenta font-extrabold border-b-4': activeTab === 'new', 'border-transparent text-gray-600 hover:text-magenta': activeTab !== 'new'}"
                    class=" px-4 text-base font-semibold transition">
                    🍰 New Order
                </button>

                <button @click="activeTab = 'history'"
                    :class="{'border-magenta text-magenta font-extrabold border-b-4': activeTab === 'history', 'border-transparent text-gray-600 hover:text-magenta': activeTab !== 'history'}"
                    class=" px-4 text-base font-semibold transition">
                    🧾 History
                </button>
            </div>

            <div x-show="activeTab === 'new'" class="flex flex-col flex-1 min-h-0">
                
                <h4 class="font-extrabold text-lg text-header mb-3 flex-shrink-0">Cake & Main Product Selection:</h4>
            <div class="overflow-x-auto py-1 whitespace-nowrap  border-b border-pink-200">
    @php
        // Get the current category filter from the request, defaulting to 'All Cakes'
        $currentCategory = request('category')
    @endphp
        @foreach($categories as $category)
        
        <a href="{{ route('pos.main', ['category' => $category, 'page' => 1]) }}"
           style="font-size:10.8px;" class="inline-block  px-2 py-2 font-semibold rounded-full transition-all duration-150 ease-in-out 
           {{ $category == $currentCategory ? 'bg-magenta text-white shadow-md' : 'bg-white text-magenta border border-pink-300 hover:bg-pink-50' }}">
            {{ $category }}
        </a>
            @endforeach
        </div>
        

                <div class="flex-1 overflow-y-auto py-1 min-h-0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 pb-2">
                        @forelse ($products as $product)
                            @php
                                $grossPrice = $product->price;
                            @endphp

                            <div> 
                                <form method="POST" action="{{ route('pos.add_item', $product) }}">
                                    @csrf
                                    <button type="submit"
                                        class="product-card-hover flex flex-col items-center w-full h-full p-3 bg-white border border-pink-200 rounded-xl shadow-lg transition text-center shrink-0 disabled:opacity-40 disabled:cursor-not-allowed"
                                        {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                        @if ($product->image_path)
                                            <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" width="90" height="90" class="w-24 h-24 object-cover rounded-xl shadow-inner mb-2">
                                        @else
                                            <div class="w-24 h-24 bg-soft-pink rounded-xl flex items-center justify-center text-xs text-magenta font-semibold mb-2">Image N/A</div>
                                        @endif
                                        <p class="font-bold text-sm leading-tight text-gray-800 line-clamp-2">{{ $product->name }}</p>
                                        <p style="font-size:10px;" class="font-bold leading-tight text-gray-500 line-clamp-2">{{ $product->category }}</p>
                                        <p class="text-base text-magenta font-extrabold mt-1">₱{{ number_format($grossPrice, 2) }}</p>
                                        <p class="text-xs text-gray-500 mt-auto">Stock: <span class="font-semibold {{ $product->stock_quantity <= 5 ? 'text-red-500' : 'text-green-600' }}">{{ $product->stock_quantity }}</span></p>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="col-span-full text-gray-500 p-4 text-center">No main products available at the moment.</p>
                        @endforelse
                    </div>
                </div>

                <div class=" border-t border-pink-200  flex justify-center shrink-0">
                    {{ $products->links() }}
                </div>
            
                <div class="mt-4 mb-4 p-4 border rounded-xl bg-soft-pink shadow-inner flex-shrink-0">
                    <h4 class="font-extrabold text-l text-header mb-3">🎁 Add Accessories to Order</h4>
                    
                    @if($accessoryProducts->count() > 0)
                        @php
                            // Get cart item IDs for filtering
                            $cartItemIds = !empty($cart) ? array_column($cart, 'id') : [];
                            
                            // Filter accessories NOT already in cart
                            $suggestedAccessories = $accessoryProducts->filter(function($accessory) use ($cartItemIds) { 
                                return !in_array($accessory->id, $cartItemIds); 
                            });
                        @endphp

                        @if($suggestedAccessories->count() > 0)
                            <div style="height:70px;">
                                <div class="flex flex-wrap ">
                                    @foreach($suggestedAccessories as $accessory)
                                        <div> 
                                            <form method="POST" action="{{ route('pos.add_item', $accessory->id) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" 
                                                        style="font-size:10px;" class=" px-1 py-1 {{ $accessory->stock_quantity > 0 ? 'bg-white hover:bg-pink-50 text-magenta' : 'bg-gray-100 text-gray-400' }}  font-medium rounded-full shadow-sm hover:shadow transition whitespace-nowrap border border-pink-300"
                                                        {{ $accessory->stock_quantity <= 0 ? 'disabled' : '' }}
                                                        title="{{ $accessory->category }} - ${{ number_format($accessory->price, 2) }}">
                                                    + {{ Str::limit($accessory->name, 20) }} 
                                                    <span class="font-bold ml-1">${{ number_format($accessory->price, 2) }}</span>
                                                    @if($accessory->stock_quantity <= 5 && $accessory->stock_quantity > 0)
                                                        <span class="ml-1 text-xs text-red-500">▼</span>
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">All available accessories are already in your cart.</p>
                        @endif
                        
                        @if(!empty($cart))
                            @php
                                $cartAccessories = collect($cart)->filter(function($item) use ($accessoryProducts) {
                                    return $accessoryProducts->contains('id', $item['id']);
                                });
                            @endphp
                            
                            @if($cartAccessories->count() > 0)
                                <div class="mt-3 pt-3 border-t border-pink-300">
                                    <p class="text-sm text-magenta font-semibold mb-2">In Cart:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($cartAccessories as $item)
                                            <span class="px-2 py-1 bg-magenta text-white text-xs rounded-full">
                                                {{ $item['name'] }} (x{{ $item['quantity'] }})
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    @else
                        <p class="text-gray-500 text-sm">No accessories available at the moment.</p>
                    @endif
                </div>

                            <div class="mt-4 mb-4 p-4 border rounded-xl bg-soft-pink shadow-inner flex-shrink-0">
                                <h4 class="font-extrabold text-lg text-header mb-3">Current Order Summary</h4>
                                
                                <div class="h-48 overflow-y-auto bg-white p-3 rounded-lg border border-pink-300">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="font-bold text-gray-700 border-b">
                                                <th class="text-left py-1">Item</th>
                                                <th class="text-center">Price</th>
                                                <th class="text-center">VAT</th>
                                                <th class="text-center">QTY</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $taxRate = 0.12;
                                                $vatFactor = 1 + $taxRate;
                                            @endphp
                                            @forelse ($cart as $item)
                                                @php
                                                    $grossPriceUnit = $item['price'];
                                                    $netPriceUnit = $grossPriceUnit / $vatFactor;
                                                    $vatPerUnit = $grossPriceUnit - $netPriceUnit;
                                                    $itemGrandTotal = $grossPriceUnit * $item['quantity'];
                                                    // This check needs to be flexible since categories are removed.
                                                    // It correctly concatenates both product lists to check stock.
                                                    $allProducts = $products->concat($accessoryProducts); 
                                                    $productStock = $allProducts->firstWhere('id', $item['id']);
                                                    $availableStock = $productStock ? $productStock->stock_quantity : (isset($item['stock_quantity']) ? $item['stock_quantity'] : 99999);
                                                @endphp

                                                <tr class="border-b border-pink-50">
                                                    <td class="text-left py-2">{{ $item['name'] }}</td>
                                                    <td class="text-center">₱{{ number_format($netPriceUnit, 2) }}</td>
                                                    <td class="text-center">₱{{ number_format($vatPerUnit, 2) }}</td>
                                                    <td class="text-center">
                                                        <div class="flex items-center justify-center space-x-1">
                                                            @if ($item['quantity'] > 1)
                                                                <form method="POST" action="{{ route('pos.update_item', $item['id']) }}" class="inline-block">
                                                                    @csrf
                                                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                                                    <button type="submit" title="Remove one" class="text-red-500 hover:text-red-700 font-bold text-base transition">( - )</button>
                                                                </form>
                                                            @else
                                                                <form method="POST" action="{{ route('remove_item', $item['id']) }}" class="inline-block">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" title="Remove item" class="text-red-500 hover:text-red-700 font-bold text-base transition">( - )</button>
                                                                </form>
                                                            @endif
                                                            <span class="font-bold text-sm mx-1">{{ $item['quantity'] }}</span>
                                                            <form method="POST" action="{{ route('pos.add_item', $item['id']) }}" class="inline-block">
                                                                @csrf
                                                                <button type="submit" title="Add one" class="text-green-500 hover:text-green-700 font-bold text-base transition disabled:text-gray-400" {{ $item['quantity'] >= $availableStock ? 'disabled' : '' }}>( + )</button>
                                                            </form>
                                                        </div>
                                                    </td>

                                                    <td class="text-right font-extrabold text-gray-800">₱{{ number_format($itemGrandTotal, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-black-500 text-center py-4">Cart is waiting for a treat!</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div x-show="activeTab === 'history'" class="flex-1 overflow-y-auto min-h-0">
                            @include('cashier.transaction-history', ['pastTransactions' => app(\App\Http\Controllers\PosController::class)->getPastTransactions(request())])
                        </div>
                    </div>
                </div> 
                
                <div class="w-full lg:w-1/4 px-3">
                    <div class="bg-white shadow-2xl rounded-xl p-4 h-[90vh] flex flex-col sticky top-4">
                        <h3 class="text-xl font-extrabold text-header mb-4 border-b border-pink-200 pb-2">Cashier</h3>
                        <div class="mb-6 p-4 bg-pink-100 border border-pink-300 rounded-xl text-center shadow-inner">
                            @php $user = Auth::user(); @endphp
                            <img src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : asset('img/default-avatar.png') }}" alt="Cashier Photo" class="w-20 h-20 object-cover rounded-full mx-auto mb-3 shadow-md border-4 border-magenta ring-2 ring-pink-300">
                            <p class="text-xl font-extrabold text-magenta">{{ $user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-pink-600 font-semibold mt-1">Position: {{ ucfirst($user->role ?? 'N/A') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Joined: {{ Carbon::parse($user->created_at)->format('M d, Y') }}</p>
                        </div>

                        <h3 class="text-l font-extrabold text-header mb-4 border-b border-pink-200 pb-2">Daily Performance</h3>
                        <div class="mb-4">
                            <p class="text-xs text-gray-500">Current Date & Time:</p>
                            <p class="text-l font-extrabold text-gray-900" id="realtime-clock">{{ Carbon::now()->format('Y-m-d h:i:s A') }}</p>
                        </div>
                        <div class=" p-2 bg-green-50 border-l-4 border-green-500 rounded-lg shadow-sm">
                            <p class="text-sm text-green-700 font-semibold">Today's Revenue:</p>
                            <p class="text-2xl font-extrabold text-green-800">₱{{ number_format($salesToday ?? 0, 2) }}</p>
                        </div>
                        <div class="p-2 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg shadow-sm">
                            <p class="text-xs text-yellow-700 font-semibold">Orders Confirmed:</p>
                            <p class="text-2xl font-extrabold text-yellow-800">{{ $dailyOrders ?? 0 }}</p>
                            
                        </div>

                        <div class="flex-1 flex flex-col justify-end py-2">
                            
    <h3 class="text l font-extrabold text-header mb-4 border-b border-pink-200 pb-2">Orders Summary</h3>
    
    <div class="mb-3 p-3 bg-white rounded-lg border border-pink-200 shadow-sm flex-shrink-0">
        <label for="discount_type" class="block text-sm font-extrabold text-magenta mb-1">Customer Discount:</label>
        
        <form id="discount-form" method="POST" action="{{ route('pos.apply_discount') }}">
            @csrf
            <select name="discount_type" id="discount_type" class="w-full border-pink-300 rounded-lg shadow-inner text-sm focus:border-magenta focus:ring-magenta bg-pink-50">
                <option value="none" {{ session('current_discount_type', 'none') == 'none' ? 'selected' : '' }}>No Discount (Standard)</option>
                <option value="sc" {{ session('current_discount_type') == 'sc' ? 'selected' : '' }}>Senior Citizen (20% & VAT Exempt)</option>
                <option value="pwd" {{ session('current_discount_type') == 'pwd' ? 'selected' : '' }}>PWD (20% & VAT Exempt)</option>
            </select>
        </form>
    </div>
    
    <script>
        document.getElementById('discount_type').addEventListener('change', function() {
            document.getElementById('discount-form').submit();
        });
    </script>

    <div class="space-y-1 flex-shrink-0 mt-4">
        <div class="flex justify-between text-base font-semibold text-gray-700 border-t pt-2 border-pink-200">
            <span>SUBTOTAL (Net):</span>
            <span>₱{{ number_format($subTotal, 2) }}</span>
        </div>
        
        <div class="flex justify-between text-sm font-semibold {{ $vatExemptAmount > 0 ? 'text-red-600' : 'text-gray-700' }}">
            <span>VAT {{ $vatExemptAmount > 0 ? 'EXEMPTION' : '(12%)' }}:</span>
            <span>{{ $vatExemptAmount > 0 ? '(-)' : '' }} ₱{{ number_format($vatExemptAmount > 0 ? $vatExemptAmount : $vatTax, 2) }}</span>
        </div>

        <div class="flex justify-between text-sm font-semibold text-red-600">
            <span>20% DISCOUNT:</span>
            <span>(-) ₱{{ number_format($discountAmount, 2) }}</span>
        </div>
    </div>

    <div class="flex justify-between text-xl font-extrabold text-white bg-magenta p-2 rounded-lg mt-3 shadow-md flex-shrink-0">
        <span>FINAL TOTAL:</span>
        <span>₱{{ number_format($finalTotal, 2) }}</span>
    </div>

    <form method="POST" action="{{ route('pos.checkout') }}" class="mt-4 flex-shrink-0">
        @csrf
        <button type="submit"
            class="w-full bg-magenta text-white font-extrabold py-3 rounded-xl shadow-xl hover:bg-pink-700 transition disabled:opacity-50 disabled:cursor-not-allowed text-xl"
            {{ empty($cart) ? 'disabled' : '' }}>
            PROCEED TO PAYMENT
        </button>
    </form>
</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const datePart = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
            const timePart = now.toLocaleTimeString('en-US', timeOptions);
            const clockElement = document.getElementById('realtime-clock');

            if (clockElement) {
                clockElement.textContent = `${datePart} ${timePart}`;
            }
        }

        window.onload = function() {
            updateClock();
            setInterval(updateClock, 1000);
        };
    </script>

</x-app-layout>