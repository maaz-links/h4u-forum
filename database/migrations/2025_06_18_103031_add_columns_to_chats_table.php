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
            $table->dropColumn('is_archived');
            $table->timestamp('unlocked_at')->nullable()->after('unlocked');
            $table->unsignedSmallInteger('send_feedback_reminder')->default(0)->after('unlocked_at');
        });

        DB::statement("UPDATE `chats` SET `unlocked_at` = `updated_at` WHERE `unlocked` = 1");
        DB::statement("UPDATE `chats` SET `send_feedback_reminder` = 1 WHERE `unlocked` = 1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('unlocked_at');
            $table->boolean('is_archived')->default(false)->after('user2_id');
            $table->dropColumn('send_feedback_reminder');
        });
    }
};
