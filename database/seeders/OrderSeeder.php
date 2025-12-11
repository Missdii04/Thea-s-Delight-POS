<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User; // <--- MUST BE PRESENT
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get the IDs of your existing cashiers
        $cashierIds = User::where('role', 'cashier')->pluck('id');

        if ($cashierIds->isEmpty()) {
            // ---> IF THIS MESSAGE ISN'T PRINTING, THE CODE IS FAILING SILENTLY
            echo "Error: No cashiers found with role 'cashier'. Cannot create orders.\n";
            return;
        }

        // 2. Create 500 orders and their line items
        Order::factory()->count(500)
            ->state(function (array $attributes) use ($cashierIds) {
                // Ensure cashier_id is one of the created cashiers
                return ['cashier_id' => $cashierIds->random()]; 
            })
            ->create()
            ->each(function ($order) {
                // Create 1 to 5 order items for each order
                OrderItem::factory()->count(rand(1, 5))->create([
                    'order_id' => $order->id,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);
            });
    }
}