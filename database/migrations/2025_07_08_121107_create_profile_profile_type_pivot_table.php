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
        Schema::create('profile_profile_type_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('user_profile_id');
            $table->unsignedBigInteger('profile_type_id');
            
            $table->foreign('user_profile_id')->references('id')->on('user_profiles')->onDelete('cascade');
            $table->foreign('profile_type_id')->references('id')->on('profile_types')->onDelete('cascade');
            
            $table->primary(['user_profile_id', 'profile_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_profile_type_pivot');
    }
};
