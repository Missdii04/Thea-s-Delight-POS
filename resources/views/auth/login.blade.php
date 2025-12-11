<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Thea's Delight - Login</title>

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
        .text-custom-magenta { color: #660733ff; } /* Primary text/header color */
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
            max-width: 800px; /* Max width of the overall box */
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

        <div class="content-panel bg-white p-8 lg:p-12">
            @if (session('status'))
        <div class="bg-pink-100 border border-magenta text-magenta p-4 rounded-lg mb-4 text-center font-semibold">
            {{ session('status') }}
        </div>
    @endif
            <h1 class="text-3xl font-bold text-custom-magenta mb-2">
                Welcome Back!
            </h1>
            <p class="mb-6 text-custom-dark-text">
                Log in to your account
            </p>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input 
                        id="email" 
                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 placeholder-custom" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username" 
                        style="height: 50px;  background-color: white; border-color: #f72397ff; border-width: 1px;" 
                    />
                    @error('email')
                        <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input 
                        id="password" 
                        class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50 placeholder-custom"
                        type="password"
                        name="password"
                        required 
                        autocomplete="current-password"
                        style="height: 50px; border-color: #f72397ff; border-width: 1px;" 
                    />
                    @error('password')
                        <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <div class="text-right">
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-gray-500 hover:text-custom-magenta" href="{{ route('password.request') }}">
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <button type="submit" style="height: 50px;" class="w-full bg-custom-pink-button hover:bg-custom-pink-hover text-custom-magenta font-bold rounded-md shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300 mb-4">
                    Log In
                </button>
                <div style="text-align: center; height: 50px;" class="w-full flex items-center justify-center bg-custom-light-register text-custom-magenta font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300">
                <a href="{{ route('register') }}" >
                    Register New Staff
                </a>
    </div>

            </form>
        </div>
    </div>
</body>
</html>