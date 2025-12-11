<?php

// database/factories/OrderItemFactory.php
namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        // 1. Get a random product from the database
        // This ASSUMES the ProductSeeder has already run.
        $product = Product::inRandomOrder()->first();
        
        // Safety check to prevent crashing if products table is empty
        if (!$product) {
            // Fallback for safety if the ProductSeeder hasn't run yet
            return [ 
                'product_id' => $this->faker->numberBetween(101, 120), 
                'product_name' => 'Placeholder Product',
                'price' => $this->faker->randomFloat(2, 5, 50),
                'quantity' => 1,
                'total' => $this->faker->randomFloat(2, 5, 50),
            ];
        }

        $quantity = $this->faker->numberBetween(1, 5);
        $total = $quantity * $product->price; 

        return [
            // 2. Use the real product ID and name
            'product_id' => $product->id, 
            'product_name' => $product->name, // <--- YOUR REAL PRODUCT NAME
            'price' => $product->price,              // <--- YOUR REAL PRODUCT PRICE
            'quantity' => $quantity,
            'total' => $total,
        ];
    }
}