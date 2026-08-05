<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('price_alerts', function (Blueprint $table) {
        // Drop the old columns
        $table->dropColumn(['email', 'target_price']);
        
        // Add the new user_id column (constrained to the users table)
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    });
}

public function down(): void
{
    Schema::table('price_alerts', function (Blueprint $table) {
        // Reverse the changes if you ever need to rollback
        $table->string('email');
        $table->decimal('target_price', 8, 2);
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
};
