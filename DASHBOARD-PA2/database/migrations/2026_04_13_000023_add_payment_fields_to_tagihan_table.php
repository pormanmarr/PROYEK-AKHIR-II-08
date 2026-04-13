<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->string('transaction_id', 100)->nullable()->after('periode');
            $table->string('payment_method', 50)->nullable()->after('transaction_id');
            $table->timestamp('payment_date')->nullable()->after('payment_method');
            $table->string('payment_status', 20)->default('belum_bayar')->after('payment_date');
            // belum_bayar, pending, lunas, gagal, cancelled
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'payment_method', 'payment_date', 'payment_status']);
        });
    }
};
