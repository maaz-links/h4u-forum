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
        Schema::table('user_profiles', function (Blueprint $table) {
            
            $table->dropColumn([
                'available_for',
                'date_of_birth',
            ]);
            //$table->smallInteger('gender')->default(0);
            $table->text('description')->nullable()->after('gender');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Add social links columns
            $table->string('facebook')->nullable()->after('description');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('telegram')->nullable()->after('instagram');
            $table->string('tiktok')->nullable()->after('telegram');
            $table->string('onlyfans')->nullable()->after('tiktok');
            $table->string('personal_website')->nullable()->after('onlyfans');
            
            // Add physical attributes columns
            $table->integer('height')->default(170)->after('personal_website');
            $table->integer('shoe_size')->default(45)->after('height');
            $table->string('eye_color')->default('brown')->after('shoe_size');
            $table->string('dress_size')->default('M')->after('eye_color');
            $table->integer('weight')->default(70)->after('dress_size');
            
            // Add flags and statuses columns
            $table->smallInteger('is_user_model')->default(0)->after('weight');
            $table->smallInteger('top_profile')->default(0)->after('is_user_model');
            $table->smallInteger('verified_female')->default(0)->after('top_profile');
            $table->smallInteger('verified_profile')->default(0)->after('verified_female');
            $table->smallInteger('visibility_status')->default(0)->after('verified_profile');
            $table->smallInteger('notification_pref')->default(0)->after('visibility_status');
            $table->smallInteger('travel_available')->default(0)->after('notification_pref');
            
            // Add credits column
            $table->bigInteger('credits')->default(0)->after('travel_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                //'gender',
                'user_id',
                'description',
                'facebook',
                'instagram',
                'telegram',
                'tiktok',
                'onlyfans',
                'personal_website',
                'height',
                'shoe_size',
                'eye_color',
                'dress_size',
                'weight',
                'is_user_model',
                'top_profile',
                'verified_female',
                'verified_profile',
                'visibility_status',
                'notification_pref',
                'travel_available',
                'credits'
            ]);
            $table->date('date_of_birth')->nullable();
            $table->json('available_for')->nullable();
        });
    }
};
