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
        Schema::create('customer_groups_lang', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_group_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name', 32)->nullable();
            $table->text('description')->nullable();

            $table->primary(['customer_group_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_groups_lang');
    }
};
