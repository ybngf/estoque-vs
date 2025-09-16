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
        // Adicionar company_id à tabela users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->boolean('is_super_admin')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
        });

        // Adicionar company_id à tabela categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies');
        });

        // Adicionar company_id à tabela suppliers
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies');
        });

        // Adicionar company_id à tabela products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies');
        });

        // Adicionar company_id à tabela stock_movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'is_super_admin', 'last_login_at', 'last_login_ip']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
