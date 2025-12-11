<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Thea's Delight - Register</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    <style>
        /* Custom styles to match the image design */
        @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap');
        
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #FADDE6; /* Light pink background */
            min-height: 100vh; 
            display: flex;
            align-items: center; 
            justify-content: center; 
            padding: 20px; 
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
            border-radius: 1rem; 
            overflow: hidden; 
            max-width: 1000px; 
            width: 100%;
        }
        
        /* Specific style for the logo panel text */
        .logo-text-large { font-size: 2rem; line-height: 2.5rem; }
        .logo-text-small { font-size: 1.50rem; line-height: 1.75rem; }

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

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <div class="main-content-wrapper">
        
        <div class="logo-panel bg-custom-light-pink p-8 lg:p-12 flex flex-col items-center justify-center text-center text-white min-h-[300px] lg:min-h-[600px]">
            <div class="space-y-4">
                
                <img src="{{ asset('storage/logoPOS.png') }}" alt="Thea's Delight Logo" style="width: 300px; height: auto; margin: 0 auto;" />
                
                <h1 class="logo-text-large font-extrabold leading-tight">
                    Welcome New Staff!
                </h1>
                <h2 class="logo-text-small font-medium">
                    Register your account here
                </h2>
            </div>
        </div>

        <div class="content-panel bg-white p-8 lg:p-12">
            <h1 class="text-3xl font-bold text-custom-magenta mb-2">
                Join Our Team!
            </h1>
            <p class="mb-6 text-custom-dark-text">
                Register a new account
            </p>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-4" enctype="multipart/form-data">
                @csrf

                <div x-data="{ focused: false }">
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input 
                        id="name" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus 
                        autocomplete="name" 
                        
                        style="height: 40px; background-color: {{ old('name') ? 'white' : '#F4E8EE' }}; border-color: #E0E0E0; border-width: 1px;" 
                        @focus="focused = true"
                        @blur="focused = false"
                    />
                    <div x-show="focused" x-cloak class="text-xs text-gray-500 mt-1 transition duration-150">
                        Enter your full name as it appears on official documents.
                    </div>
                    @error('name')
                        <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <div x-data="{ focused: false }">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input 
                        id="email" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="username" 
                        placeholder="e.g., john.doe@theasdelight.com"
                        style="height: 50px; background-color: {{ old('email') ? 'white' : '#F4E8EE' }}; border-color: #E0E0E0; border-width: 1px;" 
                        @focus="focused = true"
                        @blur="focused = false"
                    />
                    <div x-show="focused" x-cloak class="text-xs text-gray-500 mt-1 transition duration-150">
                        Use an active email address.
                    </div>
                    @error('email')
                        <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture (Optional)</label>
                    
                    <input 
                        id="profile_picture" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50" 
                        type="file" 
                        name="profile_picture"
                        style="height: 40px; background-color: {{ old('email') ? 'white' : '#F4E8EE' }}; border-color: #E0E0E0; border-width: 1px;" 
                        @focus="focused = true"
                        @blur="focused = false"
                    />
                    <div x-show="focused" x-cloak class="text-xs text-gray-500 mt-1 transition duration-150">
                        minimum size: 100x100 pixels. Max size: 2MB. Accepted formats: JPG, PNG.
                    </div>
                    @error('profile_picture')
                        <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <div x-data="{ focused: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input 
                        id="password" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50"
                        type="password"
                        name="password"
                        required 
                        autocomplete="new-password" 
                        style="height: 50px; background-color: #F4E8EE; border-color: #E0E0E0; border-width: 1px;" 
                        @focus="focused = true"
                        @blur="focused = false"
                    />
                    <div x-show="focused" x-cloak class="text-xs text-gray-500 mt-1 transition duration-150">
                        Password must be at least 8 characters long, mix of number, letter and special character.
                    </div>
                    @error('password')
                        <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                    @enderror
                </div>

                <div x-data="{ focused: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input 
                        id="password_confirmation" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50"
                        type="password"
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password" 
                        placeholder="Re-enter your password"
                        style="height: 50px; background-color: #F4E8EE; border-color: #E0E0E0; border-width: 1px;" 
                        @focus="focused = true"
                        @blur="focused = false"
                    />
                    <div x-show="focused" x-cloak class="text-xs text-gray-500 mt-1 transition duration-150">
                        Ensure this field exactly matches the password above.
                    </div>
                    @error('password_confirmation')
                        <span class="text-red-500 text-sm mt-2">{{ $message }}</span>
                    @enderror
                </div>
                <div style="text-align: center; height: 50px; font-size: 15px;" class="w-full flex items-center justify-center bg-custom-light-register text-custom-magenta font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300">
                <a href="{{ route('login') }}"  >
                    Back to Log In
                </a>
                </div>
                <button type="submit" style="height: 50px; font-size:15px;"class="w-full  bg-custom-pink-button text-custom-magenta font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300">
                    Register
                </button>

            </form>
        </div>
    </div>
</body>
</html>