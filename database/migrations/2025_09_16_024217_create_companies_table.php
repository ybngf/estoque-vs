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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('document')->nullable(); // CNPJ
            $table->foreignId('plan_id')->nullable()->constrained('plans');
            $table->enum('status', ['active', 'inactive', 'suspended', 'trial'])->default('trial');
            $table->date('trial_ends_at')->nullable();
            $table->text('settings')->nullable(); // JSON settings
            $table->string('logo')->nullable();
            $table->string('domain')->nullable(); // para subdominio personalizado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
