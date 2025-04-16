<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('spoken_language_user_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('user_profiles')->onDelete('cascade');
            $table->tinyInteger('proficiency')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('spoken_language_user_profile');
    }
};