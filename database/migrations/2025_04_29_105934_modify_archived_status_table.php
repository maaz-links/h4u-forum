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
        Schema::table('chats', function (Blueprint $table) {

            $table->renameColumn('is_archived', 'legacy_archived');
            
            // Add new user-specific archive columns
            $table->boolean('user1_archived')->default(false)->after('user2_id');
            $table->boolean('user2_archived')->default(false)->after('user1_archived');

            $table->dropColumn('legacy_archived');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {

            $table->boolean('is_archived')->default(false);
            $table->dropColumn(['user1_archived', 'user2_archived']);
        });
    }
};
