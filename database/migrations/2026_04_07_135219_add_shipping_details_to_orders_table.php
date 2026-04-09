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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_district', 100)->nullable()->after('shipping_city');
            $table->string('shipping_subdistrict', 100)->nullable()->after('shipping_district');
            $table->string('shipping_courier', 100)->nullable()->after('customer_notes');
            $table->string('shipping_service', 50)->nullable()->after('shipping_courier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_district',
                'shipping_subdistrict',
                'shipping_courier',
                'shipping_service'
            ]);
        });
    }
};
