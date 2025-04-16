<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('europe_provinces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('europe_countries')->onDelete('cascade');
            $table->string('name');
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            //$table->unique(['country_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('europe_provinces');
    }
};