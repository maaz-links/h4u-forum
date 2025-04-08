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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            // Foreign key to users table
            //$table->string('user_email')->primary();
            //$table->foreign('user_email')->references('email')->on('users')->onDelete('cascade');
            
            // Personal information
            $table->smallInteger('gender')->default(0);
            $table->date('date_of_birth')->nullable();
            //$table->foreignId('profile_picture_id')->nullable()->constrained('attachments')->onDelete('set null');
            
            // // Location information
            // $table->string('country', 100)->nullable();
            // $table->string('province', 100)->nullable();
            // $table->string('nationality', 100)->nullable();
            
            // // Physical attributes
            // $table->decimal('height', 5, 2)->nullable()->comment('In centimeters');
            // $table->decimal('weight', 5, 2)->nullable()->comment('In kilograms');
            // $table->decimal('shoesize', 3, 1)->nullable();
            // $table->string('eye_color', 50)->nullable();
            // $table->string('dress_size', 10)->nullable();
            
            // Social information
            $table->json('available_for')->nullable();
            // $table->text('interests')->nullable();
            // $table->json('social_links')->nullable();
            // $table->string('telegram', 100)->nullable();
            // $table->text('personal_description')->nullable();
            
            // // Settings
            // $table->smallInteger('visibility_status')->default(1);
            // $table->smallInteger('notification_preference')->default(0);
            
            // // Profile status
            // $table->integer('credits')->default(0);
            // //$table->boolean('top_profile')->default(false);
            // //$table->boolean('verified')->default(false);
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};