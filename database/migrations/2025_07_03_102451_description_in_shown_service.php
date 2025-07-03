<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shown_services', function (Blueprint $table) {
            $table->text('description')->nullable()->after('image_path');
        });
    }

    public function down()
    {
        Schema::table('shown_services', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};