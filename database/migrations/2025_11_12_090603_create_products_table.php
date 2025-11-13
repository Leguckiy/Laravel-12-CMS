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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 64);
            $table->integer('quantity')->default(0);
            $table->foreignId('stock_status_id')->constrained('stock_statuses')->onDelete('restrict');
            $table->string('image', 255)->nullable();
            $table->double('price', 15, 4);
            $table->tinyInteger('sort_order')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
