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
    Schema::table('agencies', function (Blueprint $table) {
        if (!Schema::hasColumn('agencies', 'location')) {
        // Add columns if they don't exist
        $table->string('location')->nullable();
        $table->string('phone_number')->nullable();
        $table->string('email')->unique()->nullable();
        $table->string('password')->nullable();
        $table->string('type')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('agencies', function (Blueprint $table) {
        $table->dropColumn(['location', 'phone_number', 'email', 'password', 'type']);
    });
}
};
