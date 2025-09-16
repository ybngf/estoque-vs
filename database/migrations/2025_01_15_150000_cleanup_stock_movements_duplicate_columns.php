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
        Schema::table('stock_movements', function (Blueprint $table) {
            // Remove duplicate/legacy columns
            if (Schema::hasColumn('stock_movements', 'previous_quantity')) {
                $table->dropColumn('previous_quantity');
            }
            if (Schema::hasColumn('stock_movements', 'current_quantity')) {
                $table->dropColumn('current_quantity');
            }
            if (Schema::hasColumn('stock_movements', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->integer('previous_quantity')->default(0);
            $table->integer('current_quantity')->default(0);
            $table->text('description')->nullable();
        });
    }
};