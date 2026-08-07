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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            // Define product_id as nullable from the start
            $table->unsignedBigInteger('product_id')->nullable();
            // Add trip_id and foreign constraint
            $table->foreignId('trip_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
            // Add other standard rating columns you might have (e.g., rating, comment, user_id)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};