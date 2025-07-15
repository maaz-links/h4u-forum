<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('height')->nullable()->default(null)->change();
            $table->integer('shoe_size')->nullable()->default(null)->change();
            $table->string('eye_color')->nullable()->default(null)->change();
            $table->string('dress_size')->nullable()->default(null)->change();
            $table->integer('weight')->nullable()->default(null)->change();
            $table->smallInteger('travel_available')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {

            // Replace NULLs with defaults before setting NOT NULL
        DB::table('user_profiles')->whereNull('height')->update(['height' => 170]);
        DB::table('user_profiles')->whereNull('shoe_size')->update(['shoe_size' => 45]);
        DB::table('user_profiles')->whereNull('eye_color')->update(['eye_color' => 'brown']);
        DB::table('user_profiles')->whereNull('dress_size')->update(['dress_size' => 'M']);
        DB::table('user_profiles')->whereNull('weight')->update(['weight' => 70]);
        DB::table('user_profiles')->whereNull('travel_available')->update(['travel_available' => 0]);

            $table->integer('height')->default(170)->nullable(false)->change();
            $table->integer('shoe_size')->default(45)->nullable(false)->change();
            $table->string('eye_color')->default('brown')->nullable(false)->change();
            $table->string('dress_size')->default('M')->nullable(false)->change();
            $table->integer('weight')->default(70)->nullable(false)->change();
            $table->smallInteger('travel_available')->default(0)->nullable(false)->change();
        });
    }
};
