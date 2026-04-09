<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('doku_invoice_id', 64)->nullable()->after('customer_notes');
            $table->string('doku_request_id', 128)->nullable()->after('doku_invoice_id');
            $table->text('doku_payment_url')->nullable()->after('doku_request_id');
            $table->timestamp('payment_expired_at')->nullable()->after('doku_payment_url');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'doku_invoice_id',
                'doku_request_id',
                'doku_payment_url',
                'payment_expired_at',
            ]);
        });
    }
};
