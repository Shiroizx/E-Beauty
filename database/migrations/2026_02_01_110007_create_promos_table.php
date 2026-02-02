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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Kode promo
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 12, 2); // Nilai diskon
            $table->decimal('min_purchase', 12, 2)->nullable(); // Minimum pembelian
            $table->decimal('max_discount', 12, 2)->nullable(); // Maksimum diskon
            $table->integer('usage_limit')->nullable(); // Batas penggunaan total
            $table->integer('usage_per_user')->default(1); // Batas per user
            $table->integer('used_count')->default(0); // Jumlah sudah digunakan
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for promo-product relationship (optional: promo for specific products)
        Schema::create('promo_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained('promos')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['promo_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_products');
        Schema::dropIfExists('promos');
    }
};
