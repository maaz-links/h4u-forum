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
        Schema::table('europe_countries', function (Blueprint $table) {
            $table->unsignedSmallInteger('is_default')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('europe_countries', function (Blueprint $table) {
            $table->dropColumn('is_default');
        });
    }
};
