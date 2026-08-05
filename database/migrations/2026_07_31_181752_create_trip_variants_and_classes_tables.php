<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Trip variants table (handles One-Way vs Round-Trip separately for buses & flights)
        Schema::create('trip_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->enum('trip_type', ['One-Way', 'Round-Trip']);
            $table->string('departure_slot'); // e.g., Aug 14, 2026 - 1:20 PM
            $table->string('arrival_slot');   // e.g., Aug 15, 2026 - 6:45 AM
            $table->text('stop_information'); // e.g., Direct or specific layovers/bus stopovers
            $table->timestamps();
        });

        // 2. Classes and pricing tied to a specific variant (works for flight classes or bus seating tiers)
        Schema::create('trip_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_variant_id')->constrained('trip_variants')->onDelete('cascade');
            $table->string('class_name'); // e.g., VIP, Classic, Economy, First Class
            $table->decimal('price', 12, 2); // Price in FCFA
            $table->string('seat_feature')->nullable(); // e.g., Reclining seat, AC, Extra legroom
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_classes');
        Schema::dropIfExists('trip_variants');
    }
};