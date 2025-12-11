<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('POS Interface ') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-3">

                <div class="w-full lg:w-3/4 px-3 mb-6 lg:mb-0">
                    <div class="bg-white shadow-md rounded-lg p-4 h-[80vh] flex flex-col">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Admin Sale Simulation</h3>

                        <div class="flex-1 overflow-y-auto p-2 border rounded-lg bg-gray-50 mb-4">
                            <h4 class="font-semibold mb-2">Product Display</h4>
                            <div class="grid grid-cols-3 gap-4">
                                @if(isset($products))
                                    @foreach ($products as $product)
                                        <div class="p-3 bg-white border rounded shadow-sm text-center">
                                            {{ $product->name ?? 'Product Name' }} (${{ number_format($product->price ?? 0, 2) }})
                                        </div>
                                    @endforeach
                                @else
                                     <p class="text-gray-500">No products found. Add products in the 'Products' tab.</p>
                                @endif
                            </div>
                        </div>

                        <div class="p-3 border rounded-lg bg-indigo-50">
                            <h4 class="font-semibold mb-2">Transaction Cart & Total (Test)</h4>
                            <p class="text-sm">Items added here.</p>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/4 px-3">
                    <div class="bg-white shadow-md rounded-lg p-4">
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">Test Summary</h3>

                        <div class="mb-4 p-3 bg-yellow-100 border-l-4 border-yellow-500">
                            <p class="text-sm text-yellow-700">User Mode:</p>
                            <p class="text-lg font-semibold">{{ Auth::user()->name ?? 'Admin' }} (Admin Test)</p>
                            <p class="text-xs text-yellow-600">Position: System Administrator</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Date:</p>
                            <p class="text-lg font-semibold">{{ now()->format('Y-m-d') }}</p>

                            <p class="text-sm text-gray-500">Time:</p>
                            <p class="text-lg font-semibold">{{ now()->format('h:i A') }}</p>

                        <div class="mb-4 p-3 bg-gray-100 border-l-4 border-gray-500">
                            <p class="text-sm text-gray-700">Products in System:</p>
                            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ count($products ?? []) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>