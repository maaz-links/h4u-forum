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
        Schema::create('fake_profile_settings', function (Blueprint $table) {
            $table->id();
            $table->string('script_name');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('dummy_id')
                ->nullable()
                ->constrained('fake_profile_settings')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dummy_id']);
            $table->dropColumn(['dummy_id']);
        });
        Schema::dropIfExists('fake_profile_settings');
    }
};
