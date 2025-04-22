<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('profile_views', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Foreign keys
            $table->unsignedBigInteger('viewer_id');
            $table->unsignedBigInteger('viewed_id');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for performance
            $table->index('viewer_id');
            $table->index('viewed_id');
            $table->index('created_at');
            
            // Composite index for frequent queries
            $table->index(['viewed_id', 'created_at']);
            
            // Foreign key constraints
            $table->foreign('viewer_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('viewed_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('profile_views');
    }
};