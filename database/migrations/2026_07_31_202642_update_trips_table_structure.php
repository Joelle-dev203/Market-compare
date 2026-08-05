<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // Add schedules column only if it doesn't already exist
            if (!Schema::hasColumn('trips', 'schedules')) {
              
                $table->json('schedules')->nullable();
            }

            // Drop the old departure_time column if it exists
            if (Schema::hasColumn('trips', 'departure_time')) {
                $table->dropColumn('departure_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'schedules')) {
                $table->dropColumn('schedules');
            }
            $table->string('departure_time')->nullable();
        });
    }
};