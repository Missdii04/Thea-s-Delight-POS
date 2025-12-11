<style>
    .text-magenta { color: #D54F8D; }
    .bg-soft-pink { background-color: #FFF0F5; }
    .text-header { color: #D54F8D; }
    .bg-magenta { background-color: #D54F8D; }
    .hover\:bg-pink-700:hover { background-color: #C3417E; }
    .border-magenta { border-color: #D54F8D; }
    .font-figtree { font-family: 'Figtree', sans-serif; }

    .input-theme {
        border-color: #FADDE6;
        background-color: #FFF9FC;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .input-theme:focus {
        border-color: #D54F8D;
        box-shadow: 0 0 0 1px #D54F8D;
    }
</style>
<x-app-layout >
    <div class="bg-soft-pink">

    <x-slot name="header">
        <h2 class="font-semibold text-header text-xl  leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
