<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create an Admin user if one doesn't exist
        User::firstOrCreate(
            ['email' => 'dessamarieamparo@gmail.com'],
            [
                'name' => 'POS Administrator',
                'password' => Hash::make('admin123'), // Change this in production!
                'role' => 'admin',
                'is_active' => true,
                // 🛑 FIX: Explicitly set email_verified_at to NULL 
                // to trigger the OTP logic in the controller.
                'email_verified_at' => null, 
            ]
        );

        User::factory()->create(['name' => 'Clint Capule', 'email' => 'clintcapule27@gmail.com
', 'role' => 'cashier']);
        User::factory()->create(['name' => 'Dessa Amparo', 'email' => 'amparodhessa18@gmail.com', 'role' => 'cashier']);
        User::factory()->create(['name' => 'Thea Lopez', 'email' => 'altheaalban30@gmail.com
', 'role' => 'cashier']);
    }
}