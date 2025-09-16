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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('type'); // database, files, full
            $table->bigInteger('size_bytes')->default(0);
            $table->string('status'); // pending, processing, completed, failed
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_automatic')->default(false);
            $table->string('storage_path')->nullable();
            $table->json('metadata')->nullable(); // Additional backup information
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
