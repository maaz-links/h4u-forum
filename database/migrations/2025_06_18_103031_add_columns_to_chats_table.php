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
            $table->unsignedSmallInteger('send_feedback_reminder')->default(0)->after('user2_id');
            $table->dropColumn('is_archived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('user2_id');
            $table->dropColumn('send_feedback_reminder');
        });
    }
};
