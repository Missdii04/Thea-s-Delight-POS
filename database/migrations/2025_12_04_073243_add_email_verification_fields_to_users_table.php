<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Adds email verification columns conditionally).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            // 1. Add email_otp column (conditionally)
            if (!Schema::hasColumn('users', 'email_otp')) {
                // Field to store the hashed PIN
                $table->string('email_otp')->nullable()->after('remember_token'); 
            }
            
            // 2. Add otp_expires_at column (conditionally)
            if (!Schema::hasColumn('users', 'otp_expires_at')) {
                // Timestamp to track when the PIN expires
                $table->timestamp('otp_expires_at')->nullable()->after('email_otp'); 
            }
            
            // 3. Add is_email_verified column (conditionally)
            if (!Schema::hasColumn('users', 'is_email_verified')) {
                // Status flag to confirm user completed verification
                $table->boolean('is_email_verified')->default(false)->after('otp_expires_at');
            }
        });
    }

    /**
     * Reverse the migrations (Removes the columns).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Note: dropColumn can throw an error if the column is missing, 
            // but we usually assume columns exist in down() for simplicity.
            // For maximum safety, you could add checks here too.
            $table->dropColumn(['email_otp', 'otp_expires_at', 'is_email_verified']);
        });
    }
};