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
    Schema::create('vendors', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Name of the shop (e.g., "Glotelho", "Boutique d'Amadou")
        $table->string('email')->unique();
        $table->string('password');
        $table->string('type')->default('local'); // 'scraped' for websites, 'local' for quarter stores
        $table->string('location')->nullable(); // Quarter/City (e.g., "Akwa, Douala", "Mokolo, Yaoundé")
        $table->string('phone_number')->nullable(); // For local shop owners
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
