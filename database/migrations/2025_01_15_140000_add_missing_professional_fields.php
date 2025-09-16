<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing professional fields to stock_movements
        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'transaction_date')) {
                $table->date('transaction_date')->nullable()->after('created_at');
            }
            if (!Schema::hasColumn('stock_movements', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->after('user_id');
            }
            if (!Schema::hasColumn('stock_movements', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });

        // Add missing professional fields to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('job_title');
            }
            if (!Schema::hasColumn('users', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'employment_status')) {
                $table->enum('employment_status', ['active', 'inactive', 'terminated', 'on_leave'])->default('active')->after('active');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
        });

        // Add missing professional fields to companies
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'company_code')) {
                $table->string('company_code')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('companies', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('email');
            }
            if (!Schema::hasColumn('companies', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('tax_id');
            }
            if (!Schema::hasColumn('companies', 'billing_address')) {
                $table->text('billing_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('companies', 'industry')) {
                $table->string('industry')->nullable()->after('billing_address');
            }
            if (!Schema::hasColumn('companies', 'employee_count')) {
                $table->integer('employee_count')->nullable()->after('industry');
            }
            if (!Schema::hasColumn('companies', 'annual_revenue')) {
                $table->decimal('annual_revenue', 15, 2)->nullable()->after('employee_count');
            }
            if (!Schema::hasColumn('companies', 'business_type')) {
                $table->enum('business_type', ['corporation', 'llc', 'partnership', 'sole_proprietorship'])->nullable()->after('annual_revenue');
            }
            if (!Schema::hasColumn('companies', 'business_hours')) {
                $table->json('business_hours')->nullable()->after('business_type');
            }
            if (!Schema::hasColumn('companies', 'timezone')) {
                $table->string('timezone')->default('America/Sao_Paulo')->after('business_hours');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove added columns from stock_movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['transaction_date', 'approved_at']);
        });

        // Remove added columns from users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id', 'first_name', 'last_name', 'job_title', 
                'department', 'hire_date', 'employment_status', 
                'last_login_at', 'last_login_ip'
            ]);
        });

        // Remove added columns from companies
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'company_code', 'tax_id', 'registration_number', 'billing_address',
                'industry', 'employee_count', 'annual_revenue', 'business_type',
                'business_hours', 'timezone'
            ]);
        });
    }
};