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
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('reporter_id')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('reported_user_id')->constrained('users')->onDelete('cascade');
        $table->text('reason')->nullable();
        $table->text('additional_info')->nullable();
        //$table->string('status')->default('pending'); // pending, reviewed, resolved, etc.
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
