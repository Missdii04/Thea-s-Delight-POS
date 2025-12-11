<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_id', // Cashier who made the sale
        'customer_id', // Optional customer reference
        'total_amount',
        'tax_amount',
        'discount_amount',
        'payment_method',
        'status', // e.g., 'completed', 'pending'
    ];
    
    // An Order belongs to the Cashier
    public function cashier()
    {
        // Links the 'cashier_id' column on the orders table to the 'id' column on the users table.
        return $this->belongsTo(User::class, 'cashier_id'); 
    }

    // An Order can optionally belong to a Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // An Order has many item details
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function createdAt(): Attribute
{
    return Attribute::make(
        get: fn ($value) => Carbon::parse($value)->setTimezone(config('app.timezone')),
    );
}
}