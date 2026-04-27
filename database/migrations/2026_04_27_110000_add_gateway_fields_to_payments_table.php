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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('payment_method');
            $table->string('gateway_transaction_no')->nullable()->after('transaction_code');
            $table->string('gateway_response_code')->nullable()->after('gateway_transaction_no');
            $table->json('gateway_payload')->nullable()->after('gateway_response_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'gateway',
                'gateway_transaction_no',
                'gateway_response_code',
                'gateway_payload',
            ]);
        });
    }
};
