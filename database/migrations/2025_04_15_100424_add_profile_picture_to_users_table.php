<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // database/migrations/[timestamp]_add_profile_picture_to_users_table.php
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('profile_picture_id')
                ->nullable()
                ->constrained('attachments')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profile_picture_id']);
            $table->dropColumn([
                'profile_picture_id',
            ]);
        });
    }
};
