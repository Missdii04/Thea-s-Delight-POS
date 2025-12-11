<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{

    protected OtpService $otpService;

    public function __construct(OtpService $otpService) // <-- ADDED
    {
        $this->otpService = $otpService; // <-- ADDED
    }
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request, save the user, and redirect to the PIN challenge.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation for all fields, including the optional file upload
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'max:2048'], // Optional image, max 2MB
        ]);

        $profilePicturePath = null;

        // 2. Handle Profile Picture Upload
        if ($request->hasFile('profile_picture')) {
            // Store the file in the public 'profile_pictures' directory 
            // and get the storage path. (Ensure storage:link is run)
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // 3. Create User (Unverified by default)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Default role for new staff (Cashier)
            'role' => 'cashier', 
            // Store the path to the picture
            'profile_picture_path' => $profilePicturePath, 
            // Crucially, email_verified_at is NULL initially
            'email_verified_at' => null, 
        ]);

        $this->otpService->sendPin($user);

       

        // 5. Securely store the user ID in the session for the next step (PIN Controller)
        $request->session()->put('pending_verification_user_id', $user->id);
        
        // 6. Redirect to the PIN challenge route
        // This route should be defined in your routes/web.php file.
        return redirect()->route('register.pin.show');

        $otpService = app(OtpService::class); // Manually resolve or inject in constructor
        $otpService->sendPin($user);
    }
}