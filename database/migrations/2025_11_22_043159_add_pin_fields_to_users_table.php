<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/..._add_pin_fields_to_users_table.php

public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('email_otp')->nullable()->after('remember_token'); // Stores the 6-digit PIN
        $table->timestamp('otp_expires_at')->nullable()->after('email_otp'); // Stores the expiration time
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['email_otp', 'otp_expires_at']);
    });
}
};
