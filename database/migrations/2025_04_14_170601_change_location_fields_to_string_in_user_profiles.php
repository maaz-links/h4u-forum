<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
        
            // Add new string columns
            $table->string('nationality')->nullable()->after('personal_website');
            $table->string('country')->nullable()->after('nationality');
            $table->string('province')->nullable()->after('country');
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Remove the string columns
            $table->dropColumn(['nationality', 'country', 'province']);
            
        });
    }
};
