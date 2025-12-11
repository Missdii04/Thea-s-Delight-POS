@php use Carbon\Carbon; @endphp

<style> @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap'); body { font-family: 'Figtree', sans-serif; background-color: #FADDE6; } .bg-magenta { background-color: #D54F8D; } .text-magenta { color: #D54F8D; } .hover\:bg-pink-700:hover { background-color: #C3417E; } .border-magenta { border-color: #D54F8D; } .bg-soft-pink { background-color: #FFF0F5; } .text-header { color: #D54F8D; } .input-theme { border-color: #FADDE6; background-color: #FFF9FC; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); padding: 0.5rem 0.75rem; border-radius: 0.5rem; border-width: 1px; } .input-theme:focus { border-color: #D54F8D; box-shadow: 0 0 0 3px rgba(213, 79, 141, 0.2); outline: none; } .custom-select { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23D54F8D' viewBox='0 0 20 20'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1em; padding-right: 2.5rem; } </style> <x-app-layout> <x-slot name="header"> <h2 class="font-extrabold text-2xl text-header">Product Catalog Overview</h2> </x-slot>
<div class="py-12 bg-soft-pink">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="p-4 mb-4 text-sm font-semibold bg-green-500 text-white rounded-xl shadow-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-2xl rounded-xl p-6">

            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('admin.products.create') }}"
                   class="bg-magenta text-white px-4 py-2 rounded-xl font-extrabold shadow-md hover:bg-pink-700 transition">
                   + Add New Product
                </a>
            </div>

            {{-- TABS NOW USE URLS --}}
            <div class="flex space-x-4 mb-6 overflow-x-auto whitespace-nowrap">

                @php $filter = request('filter') ?? 'all'; @endphp

                <a href="{{ route('admin.products.index', ['filter' => 'all'] + request()->except('page')) }}"
                   class="py-2 px-4 text-base font-semibold border-b-4 flex-shrink-0
                   {{ $filter === 'all' ? 'border-magenta text-magenta font-extrabold' : 'border-transparent text-gray-600 hover:text-magenta' }}">
                   All Products ({{ $products->total() }})
                </a>

                <a href="{{ route('admin.products.index', ['filter' => 'cakes'] + request()->except('page')) }}"
                   class="py-2 px-4 text-base font-semibold border-b-4 flex-shrink-0
                   {{ $filter === 'cakes' ? 'border-magenta text-magenta font-extrabold' : 'border-transparent text-gray-600 hover:text-magenta' }}">
                   🍰 Cakes
                </a>

                <a href="{{ route('admin.products.index', ['filter' => 'addons'] + request()->except('page')) }}"
                   class="py-2 px-4 text-base font-semibold border-b-4 flex-shrink-0
                   {{ $filter === 'addons' ? 'border-magenta text-magenta font-extrabold' : 'border-transparent text-gray-600 hover:text-magenta' }}">
                   🎁 Add-ons
                </a>
            </div>

           
           {{-- SEARCH AND CATEGORY FILTER BAR --}}
        <form method="GET" action="{{ route('admin.products.index') }}" class="mb-6">
            {{-- Hidden field to preserve the main filter (all, cakes, or addons) --}}
            <input type="hidden" name="filter" value="{{ request('filter') }}">

    <div class="flex items-center border border-pink-200 rounded-xl overflow-hidden shadow-sm">
        
        
        
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by name, SKU, or description..."
            class="w-full px-4 py-2 input-theme border-0 shadow-none text-gray-700">
            
        <button class="bg-pink-100 hover:bg-pink-200 px-4 py-2 text-magenta font-extrabold flex-shrink-0">Search</button>
    </div>
</form>

            {{-- CATEGORY FILTER --}}
            @if ($filter !== 'all')
                <form method="GET" action="{{ route('admin.products.index') }}" class="mb-6">
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                    <select name="category" onchange="this.form.submit()" class="input-theme custom-select w-full">
                        <option value="">All {{ ucfirst($filter) }}</option>

                        @if ($filter === 'cakes')
                            <option>Refrigerated Cake</option>
                            <option>Chocolate Cake</option>
                            <option>Specialty Cake</option>
                            <option>Filipino Cake</option>
                            <option>Caramel Cake</option>
                            <option>Fruit & Vegetable Cake</option>
                            <option>Coffee Cake</option>
                            <option>Cheese Cake</option>
                        @endif

                        @if ($filter === 'addons')
                            <option>Cake Topper</option>
                            <option>Candles</option>
                            <option>Greeting Card</option>
                        @endif
                    </select>
                </form>
            @endif

            {{-- TABLE --}}
            <div class="overflow-x-auto border border-pink-200 rounded-xl shadow-lg">
                <table class="min-w-full leading-normal">
                    <thead class="bg-soft-pink">
                        <tr class="text-magenta uppercase text-sm font-extrabold">
                            <th class="py-3 px-6 text-left">Image</th>
                            <th class="py-3 px-6 text-left">Name / SKU</th>
                            <th class="py-3 px-6 text-left">Category/Details</th>
                            <th class="py-3 px-6 text-center">Price</th>
                            <th class="py-3 px-6 text-center">Stock</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm font-light">
                        @foreach ($products as $product)
                            <tr class="border-b border-pink-100 hover:bg-pink-50">

                                <td class="py-3 px-6 text-left">
                                    @if ($product->image_path)
                                        <img src="{{ Storage::url($product->image_path) }}"
                                             class="w-24 h-24 object-cover rounded-xl shadow-md border border-pink-200">
                                    @else
                                        <div class="w-24 h-24 bg-gray-100 rounded-xl flex items-center justify-center text-xs text-gray-500 border border-gray-300">N/A</div>
                                    @endif
                                </td>

                                <td class="py-3 px-6">
                                    <p class="font-extrabold">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                                </td>

                                <td class="py-3 px-6">
                                    <p class="font-semibold">{{ $product->category }}</p>
                                    {{-- Display the description below the category --}}
                                    <p class="text-xs text-gray-500 italic">{{ $product->description }}</p>
                                </td>

                                <td class="py-3 px-6 text-center font-bold text-green-700">
                                    ${{ number_format($product->price, 2) }}
                                </td>

                                <td class="py-3 px-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                        {{ $product->stock_quantity < 5 ? 'bg-red-200 text-red-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>

                                <td class="py-3 px-6 text-center">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-magenta font-bold">Edit</a>

                                 <button 
                                class="text-red-500 font-bold"
                                onclick="openDeleteModal({{ $product->id }})">
                                Delete
                            </button>
                                </td>
                            </tr>
                        @endforeach

                        <!-- Delete Confirmation Modal -->
<div id="deleteModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    <div class="bg-white p-6 rounded-lg shadow-lg w-80">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Confirm Delete</h2>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this product?</p>

        <div class="flex justify-end space-x-3">
            <button 
                onclick="closeDeleteModal()"
                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                Cancel
            </button>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION (KEEPS FILTER) --}}
            <div class="mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</div>
<script>
    function openDeleteModal(productId) {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');

        // Set the form action dynamically
        document.getElementById('deleteForm').action = `/admin/products/${productId}`;
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
</x-app-layout>