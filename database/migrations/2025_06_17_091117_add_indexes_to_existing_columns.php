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
        Schema::table('users', function (Blueprint $table) {
            $table->unique('name','users_name_unique'); 
        });
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('gender'); 
            $table->dropColumn('country');
            $table->dropColumn('province');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            
            $table->string('country')->nullable()->after('nationality');
            $table->string('province')->nullable()->after('country');
            $table->smallInteger('gender')->default(0)->after('id');
        });
        Schema::table('users', function (Blueprint $table) {

            $table->dropUnique('users_name_unique');
        });
        
    }
};
