@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    $profileUrl = Auth::user()->role === 'cashier'
        ? route('cashier.profile.edit')
        : route('profile.edit');

    $mainRoute = Auth::user()->role === 'admin'
        ? route('admin.dashboard')
        : route('pos.main');
    
    // Safely check the current route name
    $currentRouteName = Route::currentRouteName();
    
    $isCashierOnProfile = Auth::user()->role === 'cashier' && $currentRouteName === 'cashier.profile.edit';
@endphp

<link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    <style>
        /* Custom styles to match the image design */
        @import url('https://fonts.googleapis.com/css2?family=Figtree:wght@400;700;800&display=swap');
        
        /* Define custom colors */
        .bg-custom-light-pink { background-color: #e98dadff; } 
        .text-custom-magenta { color: #f314c3ff; } 
        .bg-custom-pink-button { background-color: #f9aec9; } 
        .hover\:bg-custom-pink-hover:hover { background-color: #ee337bff; } 
        .bg-custom-light-register { background-color: #f07fa6ff; } 
        .text-custom-dark-text { color: #302e2eff; } 
        .placeholder-custom::placeholder { color: #E0E0E0; } 
        
        /* Alpine modal utility for hiding before JS loads */
        [x-cloak] { display: none !important; }

        </style>
<nav x-data="{ open: false, showLogoutModal: false }"
    class="bg-pink-100  shadow-md border-b border-pink-200 sticky top-0 z-50">

    <div class="max-w-screen-xl mx-auto px-4">
        <div class="flex items-center justify-between h-20 gap-4">

            {{-- LOGO (Role-Based Routing) --}}
            <div class="flex items-center gap-2">
                <a href="{{ $mainRoute }}" class="flex items-center ">
                    <img
                        src="{{ asset('storage/logoPOS.png') }}"
                        style="height: 80px; width: 80px;"
                        alt="Logo"
                    >
                    <span style="font-size: 1rem; font-weight: 800; color: #9d174d; font-family: 'Times New Roman', Times, serif;">
                        Thea's Delight POS
                    </span>
                </a>
            </div>

            {{-- DESKTOP ADMIN TABS --}}
            @if (Auth::user()->role === 'admin')
            <div class="hidden lg:flex items-center justify-center desktop-tabs">
                
                <div class="flex space-x-1"> 

                    <div style="height: 50px;" class="flex items-center justify-center bg-custom-light-register text-white font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300">
                        <a href="{{ route('admin.dashboard') }}"
                            style="font-size:18px;" class="px-6 font-medium hover:underline">
                            Dashboard
                        </a>
                    </div>

                    <div style="height: 50px;" class="flex items-center justify-center bg-custom-light-register text-white font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300">
                        <a href="{{ route('admin.products.index') }}"
                            style="font-size:18px;" class="px-6 font-medium hover:underline">
                            Products Management
                        </a>
                    </div>

                    <div style="height: 50px;" class="flex items-center justify-center bg-custom-light-register text-white font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300">
                        <a href="{{ route('admin.reports.inventory-stock') }}"
                            style="font-size:18px;" class="px-6 font-medium hover:underline">
                            Stock Inventory
                        </a>
                    </div>

                    <div style="height: 50px;" class="flex items-center justify-center bg-custom-light-register text-white font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300">
                        <a href="{{ route('admin.users.index') }}"
                            style="font-size:18px;" class="px-6 font-medium hover:underline">
                            Cashiers Management
                        </a>
                    </div>

                    <div style="height: 50px;" class="flex items-center justify-center bg-custom-light-register text-white font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300">
                        <a href="{{ route('admin.reports.index') }}"
                            style="font-size:18px;" class="px-6 font-medium hover:underline">
                            Summary Reports
                        </a>
                    </div>
                    
                </div>
            </div>
            @endif


            {{-- USER DROPDOWN (DESKTOP) --}}
            <div class="hidden lg:flex items-center desktop-dropdown">
                <x-dropdown align="right" width="48">
                    
                    <x-slot name="trigger">
                        <button class="flex items-center justify-center bg-custom-light-register text-white font-bold rounded-md shadow-md transition duration-150 ease-in-out hover:bg-custom-pink-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-300" 
                                style="text-align: center; height: 40px; width: 220;">
                            {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                            <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <a href="{{ $profileUrl }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Profile
                        </a>
                        
                        <button
                            @click="showLogoutModal = true"
                            class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 text-sm"
                        >
                            Log Out
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
            
            {{-- HAMBURGER (VISIBLE ONLY ON MOBILE) --}}
            <div class="flex lg:hidden items-center hamburger-container">
                <button
                    @click="open = !open"
                    style="background-color: transparent; border: none; cursor: pointer; padding: 0.5rem;"
                    aria-label="Toggle Menu">
                    <img src="{{ asset('storage/hamburger.png') }}" 
                          alt="Menu" 
                          style="height: 30px; width: 30px;">
                </button>
            </div>
        </div>

        <div x-show="open" x-transition @click.outside="open = false"
             class="lg:hidden border-t border-pink-200 pb-4 mobile-menu-container">

            @if ($isCashierOnProfile)
                <a href="{{ route('pos.main') }}" class="mobile-link mt-2">Home</a>
            @endif
            
            @if(Auth::user()->role === 'admin')
                <div class="flex flex-col gap-2 mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="mobile-link">Dashboard</a>
                    <a href="{{ route('admin.products.index') }}" class="mobile-link">Products Management</a>
                    <a href="{{ route('admin.reports.inventory-stock') }}" class="mobile-link">Stock Inventory</a>
                    <a href="{{ route('admin.users.index') }}" class="mobile-link">Cashiers Management</a>
                    <a href="{{ route('admin.reports.index') }}" class="mobile-link">Summary Reports</a>
                </div>
            @endif

            <div class="mt-4 border-t pt-4">
                <a href="{{ $profileUrl }}" class="mobile-link">Profile</a>

                <button
                    @click="showLogoutModal = true"
                    class="mobile-link text-red-600 w-full text-left"
                >
                    Log Out
                </button>
            </div>
        </div>
    </div> 
    
    {{-- ⭐️ LOGOUT MODAL (GLOBAL: DESKTOP + MOBILE) - PLACED INSIDE <nav> ⭐️ --}}
    <div
        x-show="showLogoutModal"
        x-cloak
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
    >
        <div class="relative max-w-sm mx-4 bg-white rounded-2xl shadow-2xl"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90">

            <div class="px-6 py-5 bg-custom-light-pink border-black">
                <h2 class="text-lg font-bold text-custom-magenta">Confirm Logout</h2>
            </div>

            <div class="px-6 py-4 text-sm text-gray-700">
                Are you sure you want to exit this account?
            </div>

            <div class="flex justify-end gap-3 px-6 py-1 bg-gray-50 border-t">
                <button
                    @click="showLogoutModal = false"
                    
                >
                    Cancel
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="px-4 py-1 border rounded-md bg-custom-light-pink text-black hover\:bg-custom-pink-hover:hover"
                    >
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    .mobile-link {
        padding: 0.75rem 1rem;
        font-size: 1rem;
        color: #9d174d;
        border-radius: 0.375rem;
    }
    .mobile-link:hover {
        background-color: #fce7f3;
    }
    
    /* FORCE HIDE HAMBURGER ON DESKTOP */
    @media (min-width: 1024px) {
        .hamburger-container {
            display: none !important;
        }
        .mobile-menu-container {
            display: none !important;
        }
        /* Ensure desktop tabs are shown */
        .desktop-tabs {
            display: flex !important;
        }
        .desktop-dropdown {
            display: flex !important;
        }
    }
    
    /* SHOW HAMBURGER ON MOBILE */
    @media (max-width: 1023px) {
        .hamburger-container {
            display: flex !important;
        }
        /* Hide desktop tabs on mobile */
        .desktop-tabs {
            display: none !important;
        }
        .desktop-dropdown {
            display: none !important;
        }
    }
</style>