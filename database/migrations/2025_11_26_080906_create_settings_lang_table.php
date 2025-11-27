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
        Schema::create('settings_lang', function (Blueprint $table) {
            $table->unsignedBigInteger('setting_id');
            $table->unsignedBigInteger('language_id');
            $table->string('value', 255)->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary(['setting_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_lang');
    }
};
