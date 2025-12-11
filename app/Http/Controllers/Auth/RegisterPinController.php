<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\OtpService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegisterPinController extends Controller
{
    protected OtpService $otpService;

    // Inject the service via constructor for better testability and cleanliness
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Shows the PIN challenge page.
     * Uses the user ID stored securely in the session.
     */
    public function showPinForm(Request $request): View|RedirectResponse
    {
        $pendingUserId = $request->session()->get('pending_verification_user_id');

        // CRITICAL CHECK 1: Ensure we have a user pending verification
        if (!$pendingUserId) {
            // No user ID in session, redirect to restart registration/login flow
            return redirect()->route('login')->with('error', 'Verification flow timed out or was interrupted.');
        }

        $user = User::find($pendingUserId);

        // CRITICAL CHECK 2: Ensure user exists and is not already verified
        if (!$user || $user->email_verified_at) {
            // Clear session and redirect.
            $request->session()->forget('pending_verification_user_id');
            return redirect()->route('login')->with('status', 'Account already verified or not found.');
        }

        // Pass the email for display only
        return view('auth.verify-email', ['email' => $user->email]);
    }

    /**
     * Handles PIN submission, strictly using the session-based user ID.
     * This fixes the security bypass vulnerability.
     */
    public function verifyPin(Request $request): RedirectResponse
{
    // 1. Validation: Only validate the PIN, the user context comes from the session.
    $request->validate([
        // Enforce a strict 6-digit numeric PIN
        'pin' => 'required|string|digits:6', 
    ]);

    $pendingUserId = $request->session()->get('pending_verification_user_id');

    // CRITICAL CHECK 1: Retrieve user ID from secure session state
    if (!$pendingUserId) {
        return redirect()->route('login')->withErrors(['pin' => 'Verification context expired. Please start registration again.']);
    }

    $user = User::find($pendingUserId);

    // CRITICAL CHECK 2: User must exist and be unverified
    if (!$user || $user->email_verified_at) {
        $request->session()->forget('pending_verification_user_id');
        return redirect()->route('login')->with('error', 'User validation failed.');
    }

    // 2. Verify the PIN against the stored PIN using the OtpService
    if (!$this->otpService->verifyPin($user, $request->pin)) {
        Log::warning('Failed PIN verification attempt for User ID: ' . $user->id);
        // Redirect back with an error if PIN is invalid or expired
        return back()->withErrors(['pin' => 'Invalid or expired PIN. Please check your email and try again.']);
    }
    
    // 3. PIN is valid: Mark user as verified, set active, and clean up temporary data.
    $user->forceFill([
        'email_verified_at' => now(), 
        'is_active' => true, // ACTIVATE THE USER ONLY AFTER SUCCESSFUL PIN VERIFICATION
        'email_otp' => null,
        'otp_expires_at' => null,
    ])->save(); 

    // 4. Clean the session to prevent replay/re-verification
    $request->session()->forget('pending_verification_user_id');
    
    // 5. CRITICAL FIX: DO NOT log the user in. Instead, redirect them explicitly to the login route.
    // Auth::login($user); // <-- REMOVED THIS LINE
    
    // FIX: Use route('login') to ensure consistent redirection to the login page.
    return redirect()->route('login')->with('status', 'Registration complete! Your account is active. Please log in.');
}
    
    // The showSuccess method is no longer needed since verifyPin logs the user in and redirects.
    // If you keep the route, it should just redirect.
    public function showSuccess(Request $request): RedirectResponse 
    {
        return redirect()->route('login')->with('status', 'Verification successful! Please log in.');
    }

    public function resendPin(Request $request): RedirectResponse
{
    // FIX: Get user ID from the secure session key used during registration.
    $pendingUserId = $request->session()->get('pending_verification_user_id');

    if (!$pendingUserId) {
        return redirect()->route('login')->with('error', 'Session expired. Please start registration again.');
    }

    $user = User::find($pendingUserId);
    
    if (!$user || $user->email_verified_at) {
        $request->session()->forget('pending_verification_user_id');
        return redirect()->route('login')->with('error', 'Account already verified or not found.');
    }

    // ⭐️ Core Logic: Resend the PIN ⭐️
    $this->otpService->sendPin($user); // The OtpService must be injected

    return back()->with('status', 'A new verification PIN has been sent to your email.');
}
}