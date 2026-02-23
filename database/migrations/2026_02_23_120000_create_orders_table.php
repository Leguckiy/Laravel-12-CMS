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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('firstname', 32);
            $table->string('lastname', 32);
            $table->string('email', 96);
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->string('shipping_firstname', 32);
            $table->string('shipping_lastname', 32);
            $table->string('shipping_company', 60)->nullable();
            $table->string('shipping_address_1', 128);
            $table->string('shipping_address_2', 128)->nullable();
            $table->string('shipping_city', 128);
            $table->string('shipping_postcode', 10)->nullable();
            $table->foreignId('shipping_country_id')->constrained('countries')->onDelete('restrict');
            $table->json('shipping_method');
            $table->decimal('shipping_cost', 15, 4)->default(0);
            $table->json('payment_method');
            $table->decimal('subtotal', 15, 4);
            $table->decimal('total', 15, 4);
            $table->foreignId('order_status_id')->constrained('order_statuses')->onDelete('restrict');
            $table->foreignId('language_id')->nullable()->constrained('languages')->onDelete('set null');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->text('comment')->nullable();
            $table->string('ip', 40)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
