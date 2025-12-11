<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    
    


public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();
    
    $user = Auth::user();
    $role = strtolower(trim($user->role));
    
    // Clear ALL session redirects (as intended)
    session()->forget('url.intended');
    session()->save();
    
    // ⭐️ FIX: Handle OTP Redirection for Cashiers ⭐️
    if ($role === 'cashier') {
        // 1. Store the user ID in the session so the OTP controller can retrieve the user.
        $request->session()->put('pending_2fa_user_id', $user->id); 
        
        // 2. Redirect to the OTP challenge route.
        // This stops the user from accessing the main app until verification is complete.
        return redirect()->route('otp.challenge'); 
    } 
    
    // ⭐️ Admin and Default Redirection ⭐️
    // Use the route name, which automatically resolves to the correct path (/dashboard)
    return redirect()->intended(route('dashboard'));
}
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
       

    }
}
