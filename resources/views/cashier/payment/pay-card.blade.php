<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Thea's Delight - Card Payment</title>

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
        .bg-custom-light-pink { background-color: #f07fa6ff; } /* For the logo panel */
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

<div class="main-content-wrapper">
        
        <div class="logo-panel bg-custom-light-pink p-8 lg:p-12 flex flex-col items-center justify-center text-center text-white min-h-[300px] lg:min-h-[600px]">
            <div class="space-y-4">
                
                <img src="{{ asset('storage/logoPOS.png') }}" alt="Thea's Delight Logo" style="width: 300px; height: auto; margin: 0 auto;" />
                
                <h1 class="text-3xl sm:text-4xl font-extrabold mb-1 text-white text-center">
                    Thea's Delight
                </h1>
               <!-- Optional decorative elements -->
            <div class="mt-8 pt-4 border-t border-white/30 w-full max-w-xs text-center">
                <p class="text-white/90 text-sm italic">
                    "Point-Of-Sale-System"
                </p>
            </div>
            </div>
        </div>
 
    <div class="content-panel bg-white p-8 lg:p-12" >
        <h1 class="text-3xl font-bold text-custom-magenta mb-2">Card Payment</h1>
        <p class="text-3xl font-extrabold mb-6">Charging: ₱{{ number_format($totals['finalTotal'] ?? 0, 2) }}</p>

        <form method="POST" action="{{ route('pos.process.card') }}">
            @csrf           
            <div class="mb-4">
                <label for="card_number" class="text-black-500 mb-3 text-sm">Card Number</label>
                <input type="text" name="card_number" id="card_number" maxlength="16" placeholder="0000 0000 0000 0000" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight @error('card_number') border-red-500 @enderror" required>
                @error('card_number') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                 <div>
                    <label for="card_type" class="text-black-500 mb-3 text-sm">Card Type</label>
                    <select name="card_type" id="card_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                        <option value="Visa">Visa</option>
                        <option value="Mastercard">Mastercard</option>
                    </select>
                </div>
                <div>
                    <label for="card_expiry" class="text-black-500 mb-3 text-sm">Expiry (MM/YY)</label>
                    <input type="text" name="card_expiry" id="card_expiry" placeholder="01/25" maxlength="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                </div>
            </div>
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('pos.pay.select') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
                <button type="submit" class="bg-custom-light-pink text-black font-bold py-3 px-6 rounded-lg shadow-md hover:bg-custom-pink-hover transition disabled:opacity-100">
                    Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>
</body>     
</html>