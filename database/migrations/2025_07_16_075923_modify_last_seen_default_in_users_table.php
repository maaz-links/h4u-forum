<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_seen');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen')->default('2000-01-01 00:00:00');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_seen');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen')->useCurrent();
        });
    }
};
