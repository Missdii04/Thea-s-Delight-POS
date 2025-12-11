<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // 1. Logic for retrieving a Cashier ID
        $cashierId = User::where('role', 'cashier')->inRandomOrder()->value('id');
        
        // Fallback for safety (as previously discussed, AdminUserSeeder should prevent this)
        if (is_null($cashierId)) {
            $cashierId = User::inRandomOrder()->value('id'); 
        }
        
        // 2. Data generation
        $date = $this->faker->dateTimeBetween('-12 months', 'now');
        $amount = $this->faker->randomFloat(2, 50, 500);
        $tax = round($amount * 0.10, 2); // Ensure tax is rounded to 2 decimal places

        // 3. Return the COMPLETE set of required attributes
        return [
            // Required Foreign Key
            'cashier_id' => $cashierId, 
            
            // Required NOT NULL or fields needed for calculation
            'total_amount' => $amount + $tax, // <-- FIX: Must include total_amount
            'tax_amount' => $tax,              // <-- FIX: Must include tax_amount
            'discount_amount' => $this->faker->randomFloat(2, 0, 20),
            
            // Other fields (based on your orders table structure)
            'customer_id' => $this->faker->randomNumber(5, true), // Placeholder ID for the optional column
            'payment_method' => $this->faker->randomElement(['card', 'cash', 'e-wallet']),
            'status' => 'completed', // Required status field
            
            // Timestamps (Crucial for laravel-trend)
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}