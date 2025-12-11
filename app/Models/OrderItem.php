<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name', // Storing name ensures historical integrity if product name changes later
        'price', 
        'quantity',
        'total', // price * quantity
    ];
    
    // An OrderItem belongs to an Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // An OrderItem belongs to a Product (reference to the product catalog)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}