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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('key'); // Setting name
            $table->text('value'); // Setting value (JSON or text)
            $table->string('type')->default('string'); // string, json, boolean, number
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Can be accessed by API
            $table->timestamps();
            
            $table->unique(['company_id', 'key']);
            $table->index(['company_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
