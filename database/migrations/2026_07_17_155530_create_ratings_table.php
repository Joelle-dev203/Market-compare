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
    // Modify the existing table instead of trying to recreate it
    Schema::table('ratings', function (Blueprint $table) {
        if (!Schema::hasColumn('ratings', 'trip_id')) {
            $table->foreignId('trip_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
        }
        
        // Ensure product_id is nullable if it wasn't already
        $table->unsignedBigInteger('product_id')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('ratings', function (Blueprint $table) {
        $table->dropForeign(['trip_id']);
        $table->dropColumn('trip_id');
    });
}
};
