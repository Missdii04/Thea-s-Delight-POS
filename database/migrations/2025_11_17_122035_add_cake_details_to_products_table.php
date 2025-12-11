<?php

// database/migrations/..._add_cake_details_to_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // New Fields for Cake Customization
            $table->string('category')->nullable()->after('name'); // e.g., Dedication Cake, Cupcake
            $table->string('size')->nullable()->after('category'); // e.g., 6", 8", 10"
            $table->string('flavor')->nullable()->after('size'); // e.g., Chocolate, Vanilla
            $table->json('topper_options')->nullable()->after('flavor'); // Stores JSON array of available toppers (e.g., plain_candle, number_candle)
            $table->string('image_path')->nullable()->after('description'); // Path for the product image
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['category', 'size', 'flavor', 'topper_options', 'image_path']);
        });
    }
};
