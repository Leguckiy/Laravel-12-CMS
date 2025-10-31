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
        Schema::create('stock_statuses', function (Blueprint $table) {
            $table->id();
        });
        
        Schema::create('stock_statuses_lang', function (Blueprint $table) {
            $table->foreignId('stock_status_id')->constrained('stock_statuses')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('name', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_statuses_lang');
        Schema::dropIfExists('stock_statuses');
    }
};
