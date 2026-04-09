<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number', 32)->unique();

            $table->string('status', 32)->default('pending_payment');
            $table->string('payment_status', 32)->default('pending');
            $table->string('payment_method', 32);

            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('total', 12, 2);

            $table->string('shipping_name');
            $table->string('shipping_phone', 32);
            $table->string('shipping_address_line');
            $table->string('shipping_city', 100);
            $table->string('shipping_province', 100);
            $table->string('shipping_postal_code', 16);
            $table->text('customer_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
