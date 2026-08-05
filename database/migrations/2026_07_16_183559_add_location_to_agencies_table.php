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
        $table->string('location')->after('name'); // Adds the column after 'name'
    });
}

public function down(): void
{
    Schema::table('agencies', function (Blueprint $table) {
        $table->dropColumn('location');
    });
}
};
