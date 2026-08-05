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
        Schema::table('vendors', function (Blueprint $table) {
            // Check if email column already exists before trying to add it
            if (!Schema::hasColumn('vendors', 'email')) {
                $table->string('email')->unique()->after('phone_number');
            }
            
            if (!Schema::hasColumn('vendors', 'password')) {
                $table->string('password')->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['email', 'password']);
        });
    }
};