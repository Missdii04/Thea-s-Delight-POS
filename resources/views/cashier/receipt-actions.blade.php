<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Thea's Delight - Receipt</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    <style>
        /* Custom styles to match the image design */
        @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap');
        
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #FADDE6; /* Light pink background */
            /* Ensure the body takes up the full viewport height for centering */
            min-height: 100vh; 
            display: flex;
            align-items: center; /* Vertical centering */
            justify-content: center; /* Horizontal centering */
            padding: 20px; /* Add some padding for small screens */
        }
        
        /* Define custom colors */
        .bg-custom-light-pink { background-color: #f3b6cbff; } /* For the logo panel */
        .text-custom-magenta { color: #ec50bdff; } /* Primary text/header color */
        .bg-custom-pink-button { background-color: #f9aec9; } /* Log In button */
        .hover\:bg-custom-pink-hover:hover { background-color: #ee337bff; } /* Log In button hover */
        .bg-custom-light-register { background-color: #f07fa6ff; } /* Register button background */
        .text-custom-dark-text { color: #302e2eff; } /* For 'Log in to your account' */
        .placeholder-custom::placeholder { color: #E0E0E0; } /* Placeholder color */

        /* Layout styles for the two panels */
        .main-content-wrapper {
            display: flex;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 1rem; /* Rounded corners */
            overflow: hidden; /* Important for containing the panels */
            max-width: 900px; /* Max width of the overall box */
            width: 100%;
            height: auto;
        }
        
        /* Specific style for the logo panel text to match the image */
        .logo-text-large { font-size: 2.25rem; line-height: 2.5rem; }
        .logo-text-small { font-size: 1.25rem; line-height: 1.75rem; }

        /* Responsive layout for large screens */
        @media (min-width: 1024px) {
            .main-content-wrapper { flex-direction: row; }
            .logo-panel { width: 40%; }
            .content-panel { width: 60%; }
        }

        /* Responsive layout for smaller screens (stacked) */
        @media (max-width: 1023px) {
            .main-content-wrapper { flex-direction: column; }
            .logo-panel { padding: 2rem; }
        }
    </style>
</head>
<body>
@php
    use Carbon\Carbon;
    
    // The controller passes the data as 'receiptDetails', so we must use $receiptDetails
    $receiptDetails = $receiptDetails ?? []; 
    
    $orderId = $receiptDetails['order_id'] ?? 'N/A';
    $receiptNo = $receiptDetails['receiptNo'] ?? 'N/A';
    
    // ⭐️ FIX: Corrected variable typos ⭐️
    $grandTotal = $receiptDetails['totals']['finalTotal'] ?? 0;
    $change = $receiptDetails['change'] ?? 0.00;
    $received = $receiptDetails['received'] ?? $grandTotal;

    // Use Carbon for initial display and ISO format for JS parsing
    $currentTimeISO = Carbon::now()->toIso8601String();
@endphp


    <div class="max-w-md mx-auto mt-16 p-6 border rounded-lg bg-custom-pink-button shadow-2xl bg-white text-center">

        <img src="{{ asset('storage/logoPOS.png') }}" alt="Thea's Delight Logo" style="width: 130px; height: auto; margin: 0 auto;" />
        <h1 class="text-3xl font-bold text-custom-magenta mb-2">Transaction Complete!</h1>

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

        <div class="mb-6 p-4 bg-white opacity-50 rounded-lg">
            <h3 class="font-bold text-lg mb-2 border-b pb-1">Payment Summary</h3>
            <div class="text-sm  space-y-1 border-b pb-2 mb-2">
                <p style="font-size:15px;" ><strong>Receipt No.:</strong> {{ $receiptNo }}</p>
                <p style="font-size:15px;"><strong>Cashier:</strong> {{ Auth::user()->name ?? 'System' }}</p>
                {{-- Dynamic Time Display Element --}}
                <p style="font-size:15px;"><strong>Time:</strong> <span id="current-time">Loading...</span></p>
                <p style="font-size:15px;"><strong>Payment Method:</strong> {{ $receiptDetails['payment_method'] ?? 'N/A' }}</p>
            </div>

            <h4 style="font-size:15px;" class="font-bold text-sm mb-1 text-black-700">Items Processed:</h4>
            <div class="max-h-36 overflow-y-auto border rounded-md p-1 bg-white">
                <table class="w-full text-xs bg-custom-light-pink">
                    <thead>
                        <tr class="font-semibold text-gray-600 ">
                            <th class="text-left py-1 px-1">Product (SKU)</th>
                            <th class="text-center py-1 px-1">Qty</th>
                            <th class="text-right py-1 px-1">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receiptDetails['items'] ?? [] as $item)
                            <tr class="border-b last:border-b-0">
                                <td class="text-left py-1 px-1">{{ $item['name'] }}</td>
                                <td class="text-center py-1 px-1">{{ $item['quantity'] }}</td>
                                <td class="text-right py-1 px-1">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t pt-2 mt-3 space-y-1">
                <div class="flex justify-between text-sm">
                    <span>Subtotal (Net):</span>
                    <span>₱{{ number_format($receiptDetails['totals']['subTotal'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>VAT/Tax:</span>
                    <span>₱{{ number_format($receiptDetails['totals']['vatTax'] ?? 0, 2) }}</span>
                </div>
                @if (($receiptDetails['totals']['discountAmount'] ?? 0) > 0)
                    <div class="flex justify-between text-sm text-red-600">
                        <span>Discount:</span>
                        <span>-₱{{ number_format($receiptDetails['totals']['discountAmount'], 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-semibold text-base border-t pt-2">
                    <span>GRAND TOTAL:</span>
                    <span>₱{{ number_format($grandTotal, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-base mt-1">
                    <span>Amount Received:</span>
                    <span>₱{{ number_format($received, 2) }}</span>
                </div>
                @if ($change > 0)
                    <div class="flex justify-between font-extrabold text-xl text-red-600 mt-2">
                        <span>Change Due:</span>
                        <span>₱{{ number_format($change, 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <p class="mb-4 text-sm text-gray-500">How would you like to proceed?</p>

        <div class="flex flex-col space-y-4">
            
            <a href="{{ route('receipt.print.thermal') }}" class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 transition ease-in-out duration-150 flex items-center justify-center">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 14a6 6 0 110-12 6 6 0 010 12zM9 5a1 1 0 012 0v5a1 1 0 11-2 0V5zm1 10a1 1 0 110-2 1 1 0 010 2z"/></svg>
                Print Receipt (Thermal)
            </a>
            
            <a href="{{ route('receipt.generate.pdf') }}" class="w-full px-4 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition ease-in-out duration-150 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.707-10.293a1 1 0 00-1.414 1.414L9 12.414V4a1 1 0 10-2 0v8.414l-2.293-2.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L10 12.414V4a1 1 0 10-2 0v8.414l-2.293-2.293z" clip-rule="evenodd" />
                </svg>
                Download/Save PDF
            </a>

            <a href="{{ route('pos.main') }}" class="w-full px-4 py-3 text-center bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 transition ease-in-out duration-150 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Start New Sale
            </a>

            <a href="{{ route('pos.transaction.detail', ['order' => $orderId]) }}" class="w-full px-4 py-2 text-center text-sm text-indigo-600 bg-transparent border border-indigo-200 rounded-lg hover:bg-indigo-50 transition ease-in-out duration-150">
                View Full Transaction Details (ID: {{ $orderId }})
            </a>
        </div>
    </div>

    {{-- Script for Real-Time Time Display (More Robust) --}}
    <script>
        function updateTime() {
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                let now = new Date();
                
                // Format: MM/DD/YYYY HH:MM:SS AM/PM
                const formattedDate = now.toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' }); 
                const formattedTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });

                timeElement.textContent = `${formattedDate} ${formattedTime}`;
            }
        }

        setTimeout(() => {
            updateTime();
            setInterval(updateTime, 1000);
        }, 0);
    </script>
