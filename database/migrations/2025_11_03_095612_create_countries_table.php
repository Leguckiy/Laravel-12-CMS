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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso_code_2', 2)->nullable();
            $table->string('iso_code_3', 3)->nullable();
            $table->tinyInteger('postcode_required')->default(0);
            $table->tinyInteger('status')->default(0);
        });
        
        Schema::create('countries_lang', function (Blueprint $table) {
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->string('name', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries_lang');
        Schema::dropIfExists('countries');
    }
};
