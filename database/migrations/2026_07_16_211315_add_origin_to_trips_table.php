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
    Schema::table('trips', function (Blueprint $table) {
        // Add the origin column
        $table->string('origin')->after('id'); 
    });
}

public function down(): void
{
    Schema::table('trips', function (Blueprint $table) {
        // Drop the column if you need to rollback
        $table->dropColumn('origin');
    });
}
};
