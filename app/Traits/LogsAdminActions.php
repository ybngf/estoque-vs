<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

trait LogsAdminActions
{
    /**
     * Log an admin action with context
     */
    protected function logAdminAction(string $action, string $resource, array $context = []): void
    {
        $user = Auth::user();
        
        $logData = [
            'action' => $action,
            'resource' => $resource,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->getRoleNames()->first(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
            'context' => $context
        ];

        Log::channel('admin_actions')->info("Admin Action: {$action}", $logData);
    }

    /**
     * Log user creation/update
     */
    protected function logUserAction(string $action, $user, array $changes = []): void
    {
        $context = [
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'target_user_company' => $user->company->name ?? 'N/A',
            'changes' => $changes
        ];

        $this->logAdminAction($action, 'user', $context);
    }

    /**
     * Log plan creation/update
     */
    protected function logPlanAction(string $action, $plan, array $changes = []): void
    {
        $context = [
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'plan_slug' => $plan->slug,
            'monthly_price' => $plan->monthly_price,
            'yearly_price' => $plan->yearly_price,
            'changes' => $changes
        ];

        $this->logAdminAction($action, 'plan', $context);
    }

    /**
     * Log subscription creation/update
     */
    protected function logSubscriptionAction(string $action, $subscription, array $changes = []): void
    {
        $context = [
            'subscription_id' => $subscription->id,
            'company_name' => $subscription->company->name,
            'plan_name' => $subscription->plan->name,
            'status' => $subscription->status,
            'amount' => $subscription->amount,
            'billing_cycle' => $subscription->billing_cycle,
            'changes' => $changes
        ];

        $this->logAdminAction($action, 'subscription', $context);
    }

    /**
     * Log security-related actions
     */
    protected function logSecurityAction(string $action, array $context = []): void
    {
        $securityContext = array_merge([
            'severity' => 'high',
            'category' => 'security',
            'requires_review' => true
        ], $context);

        $this->logAdminAction($action, 'security', $securityContext);
    }

    /**
     * Log financial operations
     */
    protected function logFinancialAction(string $action, array $context = []): void
    {
        $financialContext = array_merge([
            'category' => 'financial',
            'requires_audit' => true
        ], $context);

        $this->logAdminAction($action, 'financial', $financialContext);
    }

    /**
     * Log bulk operations
     */
    protected function logBulkAction(string $action, string $resource, int $affectedCount, array $context = []): void
    {
        $bulkContext = array_merge([
            'affected_count' => $affectedCount,
            'bulk_operation' => true
        ], $context);

        $this->logAdminAction($action, $resource, $bulkContext);
    }

    /**
     * Log data export actions
     */
    protected function logDataExport(string $resource, array $filters = []): void
    {
        $context = [
            'export_type' => $resource,
            'filters_applied' => $filters,
            'data_sensitivity' => 'high',
            'requires_compliance_check' => true
        ];

        $this->logAdminAction('data_export', $resource, $context);
    }

    /**
     * Log permission changes
     */
    protected function logPermissionChange(string $action, $user, array $permissions): void
    {
        $context = [
            'target_user_id' => $user->id,
            'target_user_email' => $user->email,
            'permissions' => $permissions,
            'security_impact' => 'high'
        ];

        $this->logSecurityAction("permission_{$action}", $context);
    }

    /**
     * Log failed actions for security monitoring
     */
    protected function logFailedAction(string $action, string $resource, string $reason, array $context = []): void
    {
        $failureContext = array_merge([
            'success' => false,
            'failure_reason' => $reason,
            'security_alert' => true
        ], $context);

        Log::channel('security')->warning("Failed Admin Action: {$action}", [
            'action' => $action,
            'resource' => $resource,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'unknown',
            'ip_address' => request()->ip(),
            'context' => $failureContext
        ]);
    }
}