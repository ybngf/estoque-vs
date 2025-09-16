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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('plan_id')->constrained('plans');
            $table->enum('status', ['active', 'inactive', 'canceled', 'past_due', 'trialing'])->default('trialing');
            $table->decimal('amount', 8, 2);
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('external_id')->nullable(); // ID do gateway de pagamento
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
