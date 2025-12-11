<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class OtpChallengeController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the PIN entry form.
     * User context must be in the session (set by the AuthenticatedSessionController).
     */
    public function show(Request $request): View|RedirectResponse
    {
        // We look for the user ID that was successfully authenticated via password
        $pendingUserId = $request->session()->get('pending_2fa_user_id');

        if (!$pendingUserId) {
            // No user context found, send them back to login
            return redirect()->route('login')->with('error', 'Authentication flow interrupted. Please log in again.');
        }

        $user = User::find($pendingUserId);
        
        if (!$user) {
            $request->session()->forget('pending_2fa_user_id');
            return redirect()->route('login')->with('error', 'Invalid user context.');
        }

        // We re-send the PIN just in case the previous one expired
        $this->otpService->sendPin($user);

        // Pass the email for display purposes only, retrieved securely from the User model
        return view('auth.otp-challenge', ['email' => $user->email]);
    }

    /**
     * Verify the PIN and complete the user login.
     */
    public function verifyPin(Request $request): RedirectResponse
    {
        // 1. PIN validation (email is NOT validated here, as the user context is session-based)
        $request->validate([
            'pin' => 'required|digits:6',
        ]);

        $pendingUserId = $request->session()->get('pending_2fa_user_id');

        if (!$pendingUserId) {
            // User ID not found, indicating session expiry or interruption
            return redirect()->route('login')->withErrors(['pin' => 'Verification context expired. Please log in again.']);
        }

        $user = User::find($pendingUserId);

        // Security check: User must exist
        if (!$user) {
            $request->session()->forget('pending_2fa_user_id');
            Log::error("2FA attempt failed: User ID $pendingUserId not found.");
            return redirect()->route('login')->with('error', 'An internal error occurred.');
        }

        // 2. Verify the PIN using the service
        if (!$this->otpService->verifyPin($user, $request->pin)) {
            return back()->withErrors(['pin' => 'The verification PIN is invalid or has expired.']);
        }
        
        // 3. PIN is valid: Complete the login process and clean up session
        $request->session()->forget('pending_2fa_user_id');
        
        // Note: The OtpService already clears the PIN from the database on success.
        
        // Log the user in
        Auth::login($user); 

        // 4. Redirection based on role (standard POS logic)
        $targetRoute = ($user->role === 'admin') ? 'admin.dashboard' : 'pos.main';

        return redirect()->intended(route($targetRoute));
    }

    public function resendPin(Request $request): RedirectResponse
    {
        $pendingUserId = $request->session()->get('pending_2fa_user_id');

        if (!$pendingUserId) {
            // No user context found, redirect back to login
            return redirect()->route('login')->with('error', 'Session expired. Please log in again to resend PIN.');
        }

        $user = User::find($pendingUserId);
        
        if (!$user) {
            // User not found in DB, clear session and redirect
            $request->session()->forget('pending_2fa_user_id');
            return redirect()->route('login')->with('error', 'Invalid user context.');
        }

        // ⭐️ Core Logic: Use the OtpService to resend the PIN ⭐️
        $this->otpService->sendPin($user);

        // Redirect back to the challenge form with a success message
        return back()->with('status', 'A new verification PIN has been sent to your email.');
    }
}