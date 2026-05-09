<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            $table->string('tripay_reference', 191)->nullable()->after('dimukaBatchId');
            $table->string('tripay_checkout_url', 512)->nullable()->after('tripay_reference');
            $table->string('tripay_pay_code', 191)->nullable()->after('tripay_checkout_url');
            $table->string('tripay_method', 64)->nullable()->after('tripay_pay_code');

            $table->index('tripay_reference', 'idx_tagihan_tripay_ref');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            $table->dropIndex('idx_tagihan_tripay_ref');
            $table->dropColumn(['tripay_reference', 'tripay_checkout_url', 'tripay_pay_code', 'tripay_method']);
        });
    }
};
