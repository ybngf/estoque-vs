<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Período de análise
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $planFilter = $request->get('plan_filter');
        $companyFilter = $request->get('company_filter');

        // Métricas principais
        $metrics = $this->getMainMetrics($dateFrom, $dateTo, $planFilter, $companyFilter);
        
        // Dados para gráficos
        $chartData = $this->getChartData($dateFrom, $dateTo, $planFilter, $companyFilter);
        
        // Top empresas e empresas recentes
        $topCompanies = $this->getTopCompanies($dateFrom, $dateTo, $planFilter);
        $recentCompanies = $this->getRecentCompanies($dateFrom, $dateTo);
        
        // Funil de conversão
        $funnel = $this->getConversionFunnel($dateFrom, $dateTo);
        
        // Métricas de engajamento
        $engagement = $this->getEngagementMetrics($dateFrom, $dateTo);
        
        // Dados para filtros
        $plans = Plan::active()->get();

        return view('super-admin.reports.index', compact(
            'metrics', 'chartData', 'topCompanies', 'recentCompanies', 
            'funnel', 'engagement', 'plans'
        ));
    }

    private function getMainMetrics($dateFrom, $dateTo, $planFilter = null, $companyFilter = null)
    {
        $query = Subscription::whereBetween('created_at', [$dateFrom, $dateTo])
                            ->where('status', 'active');

        if ($planFilter) {
            $query->where('plan_id', $planFilter);
        }

        if ($companyFilter) {
            $query->where('company_id', $companyFilter);
        }

        $totalRevenue = $query->sum('amount');

        // Crescimento da receita (comparar com período anterior)
        $previousPeriod = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo));
        $previousFrom = Carbon::parse($dateFrom)->subDays($previousPeriod);
        $previousTo = Carbon::parse($dateFrom)->subDay();

        $previousRevenue = Subscription::whereBetween('created_at', [$previousFrom, $previousTo])
                                     ->where('status', 'active')
                                     ->sum('amount');

        $revenueGrowth = $previousRevenue > 0 
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;

        // Empresas ativas
        $totalCompanies = Company::where('status', 'active')->count();
        $newCompaniesMonth = Company::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->count();

        // Usuários ativos
        $totalUsers = User::where('active', true)->count();
        $newUsersMonth = User::whereMonth('created_at', now()->month)
                           ->whereYear('created_at', now()->year)
                           ->count();

        // Taxa de churn (cancelamentos dos últimos 30 dias)
        $cancelledSubscriptions = Subscription::where('status', 'cancelled')
                                            ->whereBetween('updated_at', [now()->subDays(30), now()])
                                            ->count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $churnRate = $activeSubscriptions > 0 
            ? ($cancelledSubscriptions / $activeSubscriptions) * 100 
            : 0;

        return [
            'total_revenue' => $totalRevenue,
            'revenue_growth' => $revenueGrowth,
            'total_companies' => $totalCompanies,
            'new_companies_month' => $newCompaniesMonth,
            'total_users' => $totalUsers,
            'new_users_month' => $newUsersMonth,
            'churn_rate' => $churnRate
        ];
    }

    private function getChartData($dateFrom, $dateTo, $planFilter = null, $companyFilter = null)
    {
        // Receita mensal dos últimos 12 meses
        $months = [];
        $revenue = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M/Y');
            
            $monthRevenue = Subscription::whereYear('created_at', $date->year)
                                      ->whereMonth('created_at', $date->month)
                                      ->where('status', 'active')
                                      ->when($planFilter, function($q) use ($planFilter) {
                                          return $q->where('plan_id', $planFilter);
                                      })
                                      ->when($companyFilter, function($q) use ($companyFilter) {
                                          return $q->where('company_id', $companyFilter);
                                      })
                                      ->sum('amount');
            
            $revenue[] = floatval($monthRevenue);
        }

        // Distribuição por planos
        $planDistribution = Plan::withCount(['subscriptions' => function($q) {
            $q->where('status', 'active');
        }])->get();

        $planNames = $planDistribution->pluck('name')->toArray();
        $planCounts = $planDistribution->pluck('subscriptions_count')->toArray();

        return [
            'months' => $months,
            'revenue' => $revenue,
            'plan_names' => $planNames,
            'plan_counts' => $planCounts
        ];
    }

    private function getTopCompanies($dateFrom, $dateTo, $planFilter = null)
    {
        return Company::with(['plan', 'subscription'])
                     ->withCount('users')
                     ->whereHas('subscription', function($q) use ($dateFrom, $dateTo, $planFilter) {
                         $q->where('status', 'active')
                           ->whereBetween('created_at', [$dateFrom, $dateTo]);
                         
                         if ($planFilter) {
                             $q->where('plan_id', $planFilter);
                         }
                     })
                     ->orderByDesc(function($q) {
                         return $q->select('amount')
                                 ->from('subscriptions')
                                 ->whereColumn('company_id', 'companies.id')
                                 ->where('status', 'active')
                                 ->limit(1);
                     })
                     ->get();
    }

    private function getRecentCompanies($dateFrom, $dateTo)
    {
        return Company::with('plan')
                     ->whereBetween('created_at', [$dateFrom, $dateTo])
                     ->orderBy('created_at', 'desc')
                     ->get();
    }

    private function getConversionFunnel($dateFrom, $dateTo)
    {
        // Simulando dados de visitantes (seria integrado com Google Analytics)
        $visitors = 1000; // Placeholder
        
        $registrations = Company::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $subscriptions = Subscription::whereBetween('created_at', [$dateFrom, $dateTo])
                                   ->where('status', 'active')
                                   ->count();

        $registrationRate = $visitors > 0 ? ($registrations / $visitors) * 100 : 0;
        $subscriptionRate = $registrations > 0 ? ($subscriptions / $registrations) * 100 : 0;

        return [
            'visitors' => $visitors,
            'registrations' => $registrations,
            'subscriptions' => $subscriptions,
            'registration_rate' => $registrationRate,
            'subscription_rate' => $subscriptionRate
        ];
    }

    private function getEngagementMetrics($dateFrom, $dateTo)
    {
        // Login semanal médio (placeholder - seria calculado com logs de login)
        $weeklyLogins = 4.2;

        // Empresas ativas nos últimos 30 dias
        $activeCompanies = Company::whereHas('users', function($q) {
            $q->where('last_login_at', '>=', now()->subDays(30));
        })->count();

        // Tempo médio de sessão (placeholder)
        $avgSessionTime = '25min';

        // Taxa de retenção (empresas que fazem login regularmente)
        $totalCompanies = Company::where('status', 'active')->count();
        $retentionRate = $totalCompanies > 0 ? ($activeCompanies / $totalCompanies) * 100 : 0;

        return [
            'weekly_logins' => $weeklyLogins,
            'active_companies' => $activeCompanies,
            'avg_session_time' => $avgSessionTime,
            'retention_rate' => $retentionRate
        ];
    }

    public function export(Request $request)
    {
        // Período de análise
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $planFilter = $request->get('plan_filter');
        $companyFilter = $request->get('company_filter');

        // Gerar dados completos
        $metrics = $this->getMainMetrics($dateFrom, $dateTo, $planFilter, $companyFilter);
        $topCompanies = $this->getTopCompanies($dateFrom, $dateTo, $planFilter);
        $recentCompanies = $this->getRecentCompanies($dateFrom, $dateTo);

        $filename = 'relatorio_super_admin_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($metrics, $topCompanies, $recentCompanies, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho do relatório
            fputcsv($file, ['RELATÓRIO SUPER ADMIN - PERÍODO: ' . date('d/m/Y', strtotime($dateFrom)) . ' a ' . date('d/m/Y', strtotime($dateTo))]);
            fputcsv($file, ['']);
            
            // Métricas principais
            fputcsv($file, ['MÉTRICAS PRINCIPAIS']);
            fputcsv($file, ['Receita Total', 'R$ ' . number_format($metrics['total_revenue'], 2, ',', '.')]);
            fputcsv($file, ['Crescimento da Receita', number_format($metrics['revenue_growth'], 1) . '%']);
            fputcsv($file, ['Total de Empresas', $metrics['total_companies']]);
            fputcsv($file, ['Novas Empresas (mês)', $metrics['new_companies_month']]);
            fputcsv($file, ['Total de Usuários', $metrics['total_users']]);
            fputcsv($file, ['Novos Usuários (mês)', $metrics['new_users_month']]);
            fputcsv($file, ['Taxa de Churn', number_format($metrics['churn_rate'], 1) . '%']);
            fputcsv($file, ['']);
            
            // Top empresas
            fputcsv($file, ['TOP EMPRESAS POR RECEITA']);
            fputcsv($file, ['Empresa', 'Plano', 'Receita Mensal', 'Usuários', 'Status']);
            
            foreach ($topCompanies->take(20) as $company) {
                fputcsv($file, [
                    $company->name,
                    $company->plan ? $company->plan->name : 'Sem plano',
                    'R$ ' . number_format($company->subscription->amount ?? 0, 2, ',', '.'),
                    $company->users_count,
                    $company->active ? 'Ativa' : 'Inativa'
                ]);
            }
            
            fputcsv($file, ['']);
            
            // Empresas recentes
            fputcsv($file, ['EMPRESAS RECENTES']);
            fputcsv($file, ['Empresa', 'Plano', 'Status', 'Data de Criação']);
            
            foreach ($recentCompanies->take(50) as $company) {
                fputcsv($file, [
                    $company->name,
                    $company->plan ? $company->plan->name : 'Sem plano',
                    $company->active ? 'Ativa' : 'Inativa',
                    $company->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}