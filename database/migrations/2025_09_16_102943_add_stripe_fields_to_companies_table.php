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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable()->after('trial_ends_at');
            $table->string('pagseguro_customer_id')->nullable()->after('stripe_customer_id');
            $table->json('payment_methods')->nullable()->after('pagseguro_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['stripe_customer_id', 'pagseguro_customer_id', 'payment_methods']);
        });
    }
};
