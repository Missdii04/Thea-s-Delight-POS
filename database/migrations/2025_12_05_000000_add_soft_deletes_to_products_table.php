<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (adds the deleted_at column).
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // This method creates the 'deleted_at' timestamp column
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations (removes the deleted_at column).
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};