<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Removed the unused model uses: Order, OrderItem
// We only call other seeders here.

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. We ONLY call the specific seeders here.
        // The logic for creating the data is moved to the respective seeder files.
        $this->call([
            AdminUserSeeder::class,
            ProductSeeder::class, // <-- Creates Admin and/or initial Cashier users
            OrderSeeder::class,     // <-- Creates the 500 dummy orders
            // You may need a ProductSeeder here as well
        ]);
        
        // **REMOVE ALL THE DUPLICATED LOGIC**
        // Remove: User::factory()->create(['name' => 'Cashier A', 'role' => 'cashier']);
        // Remove: Order::factory()->count(500)->create() loop
    }
}