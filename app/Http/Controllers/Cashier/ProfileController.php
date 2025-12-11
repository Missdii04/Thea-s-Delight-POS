<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Display the cashier profile editing form.
     */
    public function edit(Request $request): View
    {
        return view('cashier.profile-edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the cashier profile information (Password and Photo only).
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Validation: Only allow photo and password changes
        $request->validate([
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            // Standard password validation fields
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $data = [];

        // 2. Handle Photo Update
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if it exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            // Store new photo and save path
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // 3. Handle Password Update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 4. Update the user record
        if (!empty($data)) {
            $user->update($data);
            return back()->with('status', 'Profile updated successfully.');
        }

        return back()->with('status', 'No changes made.');
    }
}