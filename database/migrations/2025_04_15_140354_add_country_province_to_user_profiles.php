<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Add country_id column (nullable initially if needed)
            $table->foreignId('country_id')
                  ->nullable()
                  ->constrained('europe_countries')
                  ->onDelete('set null');
            
            // Add province_id column (nullable initially if needed)
            $table->foreignId('province_id')
                  ->nullable()
                  ->constrained('europe_provinces')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['province_id']);
            $table->dropColumn(['country_id', 'province_id']);
        });
    }
};