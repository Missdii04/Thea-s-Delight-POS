<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class PinVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $pin;

    public function __construct(User $user, string $pin)
    {
        $this->user = $user;
        $this->pin = $pin;
    }

    public function envelope(): Envelope // FIX IS HERE
    {
        // Assuming the User model passed to the constructor is stored in a public property $user
        return new Envelope(
            // Set the recipient email dynamically from the User model
            to: $this->user->email, // ⭐️ CRITICAL FIX: Set the 'To' header
            
            // It's also good practice to set a 'from' address
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            
            subject: 'Thea\'s Delight - Account Verification PIN',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pin-verification',
        );
    }
}
