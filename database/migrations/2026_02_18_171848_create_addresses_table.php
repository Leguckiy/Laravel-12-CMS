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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('firstname', 255);
            $table->string('lastname', 255);
            $table->string('company', 255)->nullable();
            $table->string('address_1', 255);
            $table->string('address_2', 255)->nullable();
            $table->string('city', 128);
            $table->string('postcode', 10);
            $table->foreignId('country_id')->constrained('countries')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
