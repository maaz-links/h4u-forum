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
        Schema::table('shops_transaction_history', function (Blueprint $table) {
            $table->string('rec_title')->nullable()->after('payment_method');
            $table->decimal('rec_price', 8, 2)->default(0)->after('rec_title');
            $table->unsignedBigInteger('rec_credits')->default(0)->after('rec_price');
            $table->unsignedSmallInteger('rewarded')->default(0)->after('rec_credits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops_transaction_history', function (Blueprint $table) {
            $table->dropColumn(['rec_title', 'rec_price', 'rec_credits','rewarded']);
        });
    }
};
