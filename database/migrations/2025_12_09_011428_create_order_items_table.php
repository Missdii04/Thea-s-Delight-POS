<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Link to the Orders table
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Product details
            $table->bigInteger('product_id')->unsigned(); // No constraint needed since you don't have a 'products' model/table yet
            $table->string('product_name');
            $table->decimal('price', 8, 2);
            $table->integer('quantity');
            $table->decimal('total', 8, 2);

            $table->timestamps();
        });
    }
    // ...
};