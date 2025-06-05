<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('moderation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('keyword or regex');
            $table->string('name')->nullable()->comment('For regex patterns');
            $table->text('pattern')->comment('Actual keyword or regex pattern');
            //$table->string('category');
            $table->string('severity')->default('medium');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('type');
            //$table->index('category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('moderation_rules');
    }
};