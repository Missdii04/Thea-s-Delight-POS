<?php

namespace App\Services;

use App\Models\User;
use App\Mail\PinVerificationMail; // CRITICAL FIX: Import the Mailable class
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OtpService
{
    public function sendPin(User $user)
    {
        // 1. Generate a 6-digit PIN (keep as string for email)
        $otp = (string)random_int(100000, 999999); 
        $expiresAt = Carbon::now()->addMinutes(5);

        // 2. Save PIN and expiration time to the database
        $user->forceFill([
            'email_otp' => Hash::make($otp), // Hash the PIN for security
            'otp_expires_at' => $expiresAt,
        ])->save();
        
        // 3. Send the email notification using the Mailable class
        // FIX: The class PinVerificationMail is now correctly imported via the 'use' statement above.
        Mail::send(new PinVerificationMail($user, $otp)); 
    }

    public function verifyPin(User $user, string $pin): bool
    {
        // 1. Check if the PIN has expired
        if (!$user->email_otp) {
        return false;
    }

    if (!$user->otp_expires_at || Carbon::now()->gt($user->otp_expires_at)) {
        
        // Optional: Clear any lingering expired OTP data
        $user->forceFill(['email_otp' => null, 'otp_expires_at' => null])->save();
        return false;
    }

        // 2. Verify the PIN against the hashed value
        if (Hash::check($pin, $user->email_otp)) {
            // Clear the PIN upon successful verification
            $user->forceFill(['email_otp' => null, 'otp_expires_at' => null])->save();
            return true;
        }
        
        return false;
    }
}