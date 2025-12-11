<x-app-layout>
    <style>
        /* Define the magenta theme colors for consistency */
        .text-magenta { color: #D54F8D; }
        .bg-magenta { background-color: #D54F8D; }
        .hover\:bg-pink-700\:hover:hover { background-color: #C3417E; }
    </style>

    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-magenta leading-tight">
            {{ __('Edit User: ') . ($user->name ?? 'N/A') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-xl p-6">
                <h1 class="text-3xl font-bold mb-6 text-magenta">✏️ Edit User/Cashier Details</h1>
                
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="max-w-xl">
                    @csrf
                    @method('PUT') 

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight @error('name') border-red-500 @enderror" required>
                        @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight @error('email') border-red-500 @enderror" required>
                        @error('email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                        <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 @error('role') border-red-500 @enderror" required>
                            <option value="cashier" {{ old('role', $user->role) == 'cashier' ? 'selected' : '' }}>Cashier</option>
                            {{-- Disable changing own role if logged-in user is an admin --}}
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }} {{ Auth::user()->id === $user->id ? 'disabled' : '' }}>Admin</option>
                        </select>
                        @error('role') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="is_active" class="block text-gray-700 text-sm font-bold mb-2">Account Status</label>
                        <select name="is_active" id="is_active" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>Active</option>
                            {{-- Disable suspending own account --}}
                            <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }} {{ Auth::user()->id === $user->id ? 'disabled' : '' }}>Suspended/Banned</option>
                        </select>
                        @error('is_active') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end border-t pt-4">
                        <button type="submit" class="bg-magenta hover:bg-pink-700\:hover text-white font-bold py-2 px-4 rounded shadow-md transition duration-150">
                            Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="ml-4 font-bold text-sm text-gray-500 hover:text-gray-800">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>