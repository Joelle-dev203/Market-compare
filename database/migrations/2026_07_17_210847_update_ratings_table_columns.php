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
    Schema::table('ratings', function (Blueprint $table) {
        // Change columns to be nullable so they don't require a value
        $table->unsignedBigInteger('product_id')->nullable()->change();
        $table->unsignedBigInteger('trip_id')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('ratings', function (Blueprint $table) {
        $table->unsignedBigInteger('product_id')->nullable(false)->change();
        $table->unsignedBigInteger('trip_id')->nullable(false)->change();
    });
}
};
