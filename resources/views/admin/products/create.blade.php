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
            {{ __('Create New Product / Add-on') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-soft-pink">
        {{-- FIX: Initialize Alpine context with unique models for each tab's data --}}
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8" 
             x-data="{ 
                 activeTab: 'cake',
                 // Unique models for Cake Tab fields
                 cakeCategory: 'Refrigerated Cake',
                 cakeDescription: '',
                 cakeSize: '8 in',
                 
                 // Unique models for Add-on Tab fields
                 addonCategory: 'Cake Topper (Wedding)',
                 addonDescription: '', 
                 
                 // COMMON FIELD INPUTS 
                 name: '',
                 sku: '',
                 price: '',
                 stock_quantity: ''
             }">
            <div class="bg-white shadow-2xl rounded-xl p-6">
                
                <h3 class="text-3xl font-extrabold mb-4 pb-2 text-header">Add New Product</h3>

                <!-- Tab Navigation (Themed) -->
                <div class="flex border-b border-pink-200 mb-6">
                    <button @click="activeTab = 'cake'"
                            :class="{'border-magenta text-magenta font-extrabold border-b-4': activeTab === 'cake', 'border-transparent text-gray-600 hover:text-magenta': activeTab !== 'cake'}"
                            class="py-2 px-4 text-base font-semibold transition">
                        🍰 Add Cake Variant
                    </button>
                    <button @click="activeTab = 'addon'"
                            :class="{'border-magenta text-magenta font-extrabold border-b-4': activeTab === 'addon', 'border-transparent text-gray-600 hover:text-magenta': activeTab !== 'addon'}"
                            class="py-2 px-4 text-base font-semibold transition">
                        🎁 Add Add-on Product
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- COMMON FIELDS --}}
                        
                        {{-- Product Name --}}
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="font-bold text-gray-700"/>
                            <!-- Applying input-theme style -->
                            <x-text-input id="name" name="name" x-model="name" class="mt-1 block w-full input-theme" type="text" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        
                        {{-- SKU --}}
                        <div>
                            <x-input-label for="sku" :value="__('SKU (Unique Identifier)')" class="font-bold text-gray-700"/>
                            <!-- Applying input-theme style -->
                            <x-text-input id="sku" name="sku" x-model="sku" class="mt-1 block w-full input-theme" type="text" required />
                            <x-input-error class="mt-2" :messages="$errors->get('sku')" />
                        </div>
                        
                        {{-- Price --}}
                        <div>
                            <x-input-label for="price" :value="__('Gross Price (Tax Inclusive)')" class="font-bold text-gray-700"/>
                            <!-- Applying input-theme style -->
                            <x-text-input id="price" name="price" x-model="price" class="mt-1 block w-full input-theme" type="number" step="0.01" min="0.01" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>
                        
                        {{-- Stock --}}
                        <div>
                            <x-input-label for="stock_quantity" :value="__('Stock Quantity')" class="font-bold text-gray-700"/>
                            <!-- Applying input-theme style -->
                            <x-text-input id="stock_quantity" name="stock_quantity" x-model="stock_quantity" class="mt-1 block w-full input-theme" type="number" min="0" required />
                            <x-input-error class="mt-2" :messages="$errors->get('stock_quantity')" />
                        </div>

                        
                        {{-- TAB-SPECIFIC FIELDS WRAPPER --}}
                        <div class="md:col-span-2">
                            
                            {{-- CAKE VARIANT TAB FIELDS --}}
                            <div x-show="activeTab === 'cake'" class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-pink-200 pt-6">
                                
                                {{-- 1. Cake Specific Category Selector --}}
                                <div>
                                    <x-input-label for="category_cake" :value="__('1. Cake Category')" class="font-bold text-gray-700"/>
                                    <!-- Applying input-theme style and custom-select class -->
                                    <select id="category_cake" x-model="cakeCategory" 
                                            x-bind:name="activeTab === 'cake' ? 'category' : ''" 
                                            x-bind:required="activeTab === 'cake'" 
                                            class="mt-1 block w-full input-theme custom-select text-gray-700">
                                        <option value="">Select Cake Type</option>
                                        {{-- Comprehensive Cake List --}}
                                        <option value="Refrigerated Cake">Refrigerated Cake</option>
                                        <option value="Chocolate Cake">Chocolate Cake</option>
                                        <option value="Specialty Cake">Specialty Cake</option>
                                        <option value="Filipino Cake">Filipino Cake</option>
                                        <option value="Caramel Cake">Caramel Cake</option>
                                        <option value="Fruit & Vegetable Cake">Fruit & Vegetable Cake</option>
                                        <option value="Coffee Cake">Coffee Cake</option>
                                        <option value="Cheese Cake">Cheesecake</option>
                                        <option value="Milk Cake">Milk Cake</option>
                                       
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('category')" />
                                </div>
                                
                                {{-- 2. Size Category Selector (New Field) --}}
                                <div>
                                    <x-input-label for="size_category" :value="__('2. Size Category')" class="font-bold text-gray-700"/>
                                    <!-- Applying input-theme style and custom-select class -->
                                    <select id="size_category" x-model="cakeSize" 
                                            x-bind:name="activeTab === 'cake' ? 'size_category' : ''" 
                                            x-bind:required="activeTab === 'cake'" 
                                            class="mt-1 block w-full input-theme custom-select text-gray-700">
                                        <option value="6 in">6 inch</option>
                                        <option value="8 in">8 inch</option>
                                        <option value="12 in">12 inch</option>
                                        
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('size_category')" />
                                </div>


                                {{-- 3. Cake Details (Flavor, Decor) --}}
                                <div class="md:col-span-2">
                                    <x-input-label for="description_cake" :value="__('3. Cake Flavor / Specific Decor Details')" class="font-bold text-gray-700"/>
                                    <!-- Applying input-theme style -->
                                    <x-text-input id="description_cake" x-model="cakeDescription" 
                                                    x-bind:name="activeTab === 'cake' ? 'description' : ''" 
                                                    x-bind:required="activeTab === 'cake'"
                                                    class="mt-1 block w-full input-theme" type="text" placeholder="e.g., Chocolate Ganache, Raspberry Filling, Special Icing" />
                                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                </div>
                            </div>

                            {{-- ADD-ON PRODUCT TAB FIELDS --}}
                            <div x-show="activeTab === 'addon'" class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-pink-200 pt-6">
                                
                                {{-- 1. Add-on Specific Category Selector --}}
                                <div class="md:col-span-2">
                                    <x-input-label for="category_addon" :value="__('1. Add-on Category')" class="font-bold text-gray-700"/>
                                    <!-- Applying input-theme style and custom-select class -->
                                    <select id="category_addon" x-model="addonCategory" 
                                            x-bind:name="activeTab === 'addon' ? 'category' : ''" 
                                            x-bind:required="activeTab === 'addon'" 
                                            class="mt-1 block w-full input-theme custom-select text-gray-700">
                                        {{-- Granular Add-on Categories --}}
                                        <option value="Cake Topper">Cake Topper</option>
                                        <option value="Candles">Candles</option>
                                        <option value="Greeting Card">Greeting Card</option>
                                
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('category')" />
                                </div>
                                
                                {{-- Placeholder for Description / Specific Details if needed --}}
                                <input type="hidden" x-bind:name="activeTab === 'addon' ? 'description' : ''" value="Add-on item">
                            </div>
                        </div>
                        
                        {{-- Image Upload --}}
                        <div class="col-span-2 border-t border-pink-200 pt-4">
                            <x-input-label for="image_file" :value="__('Product Image')" class="font-bold text-gray-700"/>
                            <input id="image_file" name="image_file" type="file" class="mt-1 block w-full border border-pink-300 rounded-md p-1 bg-white" accept="image/*" />
                            <p class="mt-1 text-xs text-gray-500">Max size 2MB. Recommended 1:1 aspect ratio.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('image_file')" />
                        </div>

                        <div class="col-span-2 flex justify-end gap-3 mt-6">
                            
                            {{-- Cancel Button (Themed) --}}
                            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-pink-100 border border-pink-300 rounded-xl font-extrabold text-sm text-magenta uppercase tracking-widest hover:bg-pink-200 focus:bg-pink-200 focus:outline-none focus:ring-2 focus:ring-magenta focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                            
                            <!-- Save Button (Themed) -->
                            <button type="submit" class="px-6 py-3 bg-magenta text-white font-extrabold rounded-xl shadow-lg hover:bg-pink-700 transition">
                                {{ __('Save Product') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>