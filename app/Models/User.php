<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable; 
use App\Models\Order; 

class User extends Authenticatable
{
    // Add all necessary traits
    use HasFactory, Notifiable, TwoFactorAuthenticatable; 

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'profile_photo_path', 
        'name',
        'email',
        'password',
        'role',         
        'is_active',     
        'is_email_verified', 
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',           
        'two_factor_recovery_codes',   
    ];

    public function getRoleAttribute($value)
{
    // Check if there's any mutator changing the value
    return $value;
}

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',           
            'is_email_verified' => 'boolean',   // Cast status field
            'otp_expires_at' => 'datetime',     // Cast OTP expiration field
            'two_factor_confirmed_at' => 'datetime', 
        ];
    }
    
    // --- RELATIONSHIPS ---
    
    /**
     * A User (Cashier) has many Orders (sales made).
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}