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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // e.g., "Riz Mémé Cassé 25kg", "Savon Ndama"
        $table->string('brand')->nullable(); // e.g., "Mayor", "Nestlé"
        $table->string('category')->nullable(); // e.g., "Alimentation", "Électronique"
        $table->string('image_url')->nullable(); // To store the path to the product image
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
