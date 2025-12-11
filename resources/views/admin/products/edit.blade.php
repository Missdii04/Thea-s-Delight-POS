@php use Carbon\Carbon; @endphp

<style>
    /* Custom Font and Theme Colors from pos_main */
    @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap');
    body { font-family: 'Figtree', sans-serif; background-color: #FADDE6; }
    .bg-magenta { background-color: #D54F8D; }
    .text-magenta { color: #D54F8D; }
    .hover\:bg-pink-700:hover { background-color: #C3417E; }
    .border-magenta { border-color: #D54F8D; }
    .bg-soft-pink { background-color: #FFF0F5; } 
    .text-header { color: #D54F8D; }

    /* Custom Input Styling to match the theme (Replaces standard border-gray-300) */
    .input-theme {
        border-color: #FADDE6; 
        background-color: #FFF9FC; 
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
        /* Apply basic border/padding for all input components */
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem; /* rounded-lg */
        border-width: 1px;
    }
    .input-theme:focus {
        border-color: #D54F8D; 
        box-shadow: 0 0 0 3px rgba(213, 79, 141, 0.2); /* Custom focus ring */
        outline: none;
    }

    /* Target specific components to ensure they inherit theme */
    .custom-select {
        /* Ensuring select elements look right */
        appearance: none; 
        background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23D54F8D'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 1.5em;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-header leading-tight">
            {{ __('Edit Product: ' . $product->name) }}
        </h2>
    </x-slot>

    <div class="py-12 bg-soft-pink">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl rounded-xl p-6">
                
                <h3 class="text-3xl font-extrabold mb-6 border-b border-pink-200 pb-2 text-header">Editing {{ $product->name }}</h3>

                <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Product Name and SKU --}}
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="font-bold text-gray-700"/>
                            <x-text-input id="name" name="name" 
                                          class="mt-1 block w-full input-theme" 
                                          type="text" 
                                          value="{{ old('name', $product->name) }}" 
                                          required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        
                        <div>
                            <x-input-label for="sku" :value="__('SKU (Unique Identifier)')" class="font-bold text-gray-700"/>
                            <x-text-input id="sku" name="sku" 
                                          class="mt-1 block w-full input-theme" 
                                          type="text" 
                                          value="{{ old('sku', $product->sku) }}" 
                                          required />
                            <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                        </div>

                        {{-- Category and Price --}}
                        <div>
    <x-input-label for="category" :value="__('Category')" class="font-bold text-gray-700"/>
    <select id="category" name="category" class="mt-1 block w-full input-theme custom-select text-gray-700" required>
        @php 
            $selectedCategory = old('category', $product->category); 
        @endphp
        
        <option value="">Select Category</option>

        {{-- CAKE CATEGORIES --}}
        <optgroup label="Cake Categories">
            <option value="Refrigerated Cake" {{ $selectedCategory == 'Refrigerated Cake' ? 'selected' : '' }}>Refrigerated Cake</option>
            <option value="Chocolate Cake" {{ $selectedCategory == 'Chocolate Cake' ? 'selected' : '' }}>Chocolate Cake</option>
            <option value="Caramel Cake" {{ $selectedCategory == 'Caramel Cake' ? 'selected' : '' }}>Caramel Cake</option>
            <option value="Filipino Cake" {{ $selectedCategory == 'Filipino Cake' ? 'selected' : '' }}>Filipino Cake</option>
            <option value="Specialty Cake" {{ $selectedCategory == 'Specialty Cake' ? 'selected' : '' }}>Specialty Cake</option>
            <option value="Fruit & Vegetable Cake" {{ $selectedCategory == 'Fruit & Vegetable Cake' ? 'selected' : '' }}>Fruit & Vegetable Cake</option>
            <option value="Coffee Cake" {{ $selectedCategory == 'Coffee Cake' ? 'selected' : '' }}>Coffee Cake</option>
            <option value="Cheese Cake" {{ $selectedCategory == 'Cheese Cake' ? 'selected' : '' }}>Cheese Cake</option>
            <option value="Milk Cake" {{ $selectedCategory == 'Milk Cake' ? 'selected' : '' }}>Milk Cake</option>
        </optgroup>

        {{-- ADD-ON CATEGORIES (THE DETAILED LIST) --}}
        <optgroup label="Add-on Categories">
            <option value="Cake Topper" {{ $selectedCategory == 'Cake Topper' ? 'selected' : '' }}>Cake Topper</option>
            <option value="Candles" {{ $selectedCategory == 'Candles' ? 'selected' : '' }}>Candles</option>
            <option value="Greeting Card" {{ $selectedCategory == 'Greeting Card' ? 'selected' : '' }}>Greeting Card</option>
        </optgroup>
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('category')" />
</div>
                        <div>
                            <x-input-label for="price" :value="__('Gross Price (Tax Inclusive)')" class="font-bold text-gray-700"/>
                            <x-text-input id="price" name="price" 
                                          class="mt-1 block w-full input-theme" 
                                          type="number" step="0.01" min="0.01" 
                                          value="{{ old('price', $product->price) }}" 
                                          required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        {{-- Stock and Description --}}
                        <div>
                            <x-input-label for="stock_quantity" :value="__('Stock Quantity')" class="font-bold text-gray-700"/>
                            <x-text-input id="stock_quantity" name="stock_quantity" 
                                          class="mt-1 block w-full input-theme" 
                                          type="number" min="0" 
                                          value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                          required />
                            <x-input-error class="mt-2" :messages="$errors->get('stock_quantity')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Details/Description (Size, Flavor, Add-on Info)')" class="font-bold text-gray-700"/>
                            <x-text-input id="description" name="description" 
                                          class="mt-1 block w-full input-theme" 
                                          type="text" 
                                          value="{{ old('description', $product->description) }}" 
                                          placeholder="e.g., Size: 8 | Flavor: Vanilla" />
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>
                        
                        {{-- Image Upload --}}
                        <div class="col-span-2 border-t border-pink-200 pt-4">
                            <x-input-label for="image_file" :value="__('Product Image (Optional, leave blank to keep current)')" class="font-bold text-gray-700"/>
                            
                            {{-- Display Current Image --}}
                            @if ($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" alt="Current Image" class="w-20 h-20 object-cover rounded-xl my-2 border border-pink-200 shadow-sm">
                            @endif
                            
                            <input id="image_file" name="image_file" type="file" class="mt-1 block w-full border border-pink-300 rounded-lg p-2 bg-pink-50" accept="image/*" />
                            <p class="mt-1 text-xs text-gray-500">Upload new image to replace current one. Max size 2MB.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('image_file')" />
                        </div>

                        
                        <div class="col-span-2 flex justify-end mt-6">
                            <button type="submit" class="px-6 py-3 bg-magenta text-white font-extrabold rounded-xl shadow-lg hover:bg-pink-700 transition">
                                {{ __('Update Product') }}
                            </button>
                            
                        <a href="{{ route('admin.products.index') }}" class="btn-cancel px-6 py-3 bg-magenta text-white font-extrabold rounded-xl shadow-lg hover:bg-pink-700 transition">Cancel Update</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>