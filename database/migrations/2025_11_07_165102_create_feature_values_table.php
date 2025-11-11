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
        Schema::create('feature_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feature_id')->index();
            $table->tinyInteger('sort_order')->default(0);
        });
        
        Schema::create('feature_values_lang', function (Blueprint $table) {
            $table->unsignedBigInteger('feature_value_id');
            $table->unsignedBigInteger('language_id');
            $table->string('value', 255);
            
            $table->primary(['feature_value_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_values_lang');
        Schema::dropIfExists('feature_values');
    }
};
