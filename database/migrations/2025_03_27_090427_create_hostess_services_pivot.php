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
        Schema::create('hostess_service_pivot', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('hostess_id');
            // $table->unsignedBigInteger('hostess_services_id');
            // $table->foreign('hostess_id')->references('id')->on('user_profiles')->onDelete('cascade');
            // $table->foreign('hostess_services_id')->references('id')->on('hostess_services')->onDelete('cascade');
            $table->foreignId('user_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('hostess_service_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostess_service_pivot');
    }
};
