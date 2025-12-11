@php use Carbon\Carbon; @endphp
<title>Thea's Delight - Profile</title>
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

    /* Custom Input Styling to match the theme */
    .input-theme {
        border-color: #FADDE6; 
        background-color: #FFF9FC; 
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
    }
    .input-theme:focus {
        border-color: #D54F8D; 
        box-shadow: 0 0 0 1px #D54F8D;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-header leading-tight">
            {{ __('Account & Profile Settings') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-soft-pink">
        <!-- FIX: Changed max-w-4xl to max-w-xl to make the editing div less wide -->
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Profile Overview Card -->
            <div class="p-4 sm:p-8 bg-white shadow-2xl rounded-xl">
                <header class="text-center pb-6 border-b border-pink-200">
                    <h2 class="text-2xl font-extrabold text-header mb-2">
                        Your Cashier Profile
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage your profile details and picture for the POS system.
                    </p>
                </header>

                <div class="mt-8">
                    <!-- PROFILE PICTURE UPLOAD AREA -->
                    <div class="flex flex-col items-center mb-10">
                        <label for="profile_photo" class="cursor-pointer">
                            @php $user = Auth::user(); @endphp
                            
                            <!-- Balanced Profile Picture Size (FIXED: w-20 h-20 on all screens) -->
                            <img src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : asset('img/default-avatar.png') }}" 
                                    alt="Profile Photo" 
                                    class="w-20 h-20 object-cover rounded-full mx-auto mb-3 shadow-xl border-4 border-magenta ring-4 ring-pink-200 transition duration-300 hover:ring-magenta">
                            
                            <p class="text-sm font-semibold text-magenta mt-2 hover:underline">Click to Change Photo</p>
                            <input id="profile_photo" 
                                    class="hidden" 
                                    type="file" 
                                    name="profile_photo" 
                                    form="update-profile-form" 
                                    accept="image/*" />
                        </label>

                        <div class="text-center mt-3">
                            <p class="text-lg font-bold text-gray-800">{{ $user->name }}</p>
                            <p class="text-sm text-pink-600">Position: {{ ucfirst($user->role) }}</p>
                        </div>
                    </div>
                    
                    <!-- USER DETAILS FORM (Assuming Livewire/Inertia/Standard form update logic) -->
                    <form id="update-profile-form" method="POST" action="{{ route('cashier.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')
                        
                        <!-- Name Field -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="font-bold text-gray-700"/>
                            <x-text-input id="name" name="name" type="text" 
                                          class="mt-1 block w-full input-theme" 
                                          :value="old('name', $user->name)" 
                                          required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Email Field -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="font-bold text-gray-700"/>
                            <x-text-input id="email" name="email" type="email" 
                                          class="mt-1 block w-full input-theme" 
                                          :value="old('email', $user->email)" 
                                          required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Save Button -->
                        <div class="flex items-center gap-4 pt-4">
                            <button type="submit" 
                                    class="px-6 py-3 bg-magenta text-white font-extrabold rounded-xl shadow-lg hover:bg-pink-700 transition">
                                {{ __('Save Changes') }}
                            </button>

                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-green-600">
                                    {{ __('Saved.') }}
                                </p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Update Password Card -->
            <div class="p-4 sm:p-8 bg-white shadow-2xl rounded-xl">
                @include('profile.partials.update-password-form')
            </div>

        </div>
    </div>
</x-app-layout>