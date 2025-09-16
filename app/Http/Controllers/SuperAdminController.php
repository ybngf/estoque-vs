<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // Métricas principais
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('status', 'active')->count();
        $totalUsers = User::count();
        $usersToday = User::whereDate('created_at', today())->count();
        
        // Métricas financeiras
        $monthlyRevenue = Subscription::where('status', 'active')
            ->whereMonth('created_at', now()->month)
            ->sum('amount') ?? 0;
            
        $lastMonthRevenue = Subscription::where('status', 'active')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount') ?? 1;
            
        $revenueGrowth = $lastMonthRevenue > 0 ? 
            round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;
        
        // Assinaturas
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $expiringSoon = Subscription::where('status', 'active')
            ->where('ends_at', '<=', now()->addDays(7))
            ->count();

        // Dados recentes
        $recentCompanies = Company::with(['plan', 'subscription'])
            ->latest()
            ->take(5)
            ->get();

        // Atividades fictícias para demonstração
        $recentActivities = collect([
            (object) ['description' => 'Nova empresa "TechCorp" criada', 'created_at' => now()->subMinutes(30)],
            (object) ['description' => 'Assinatura renovada: "StartupXYZ"', 'created_at' => now()->subHours(2)],
            (object) ['description' => 'Usuário admin criado na empresa "RetailPlus"', 'created_at' => now()->subHours(4)],
            (object) ['description' => 'Backup do sistema concluído', 'created_at' => now()->subHours(6)],
            (object) ['description' => 'Relatório mensal gerado', 'created_at' => now()->subHours(8)],
        ]);

        return view('super-admin.dashboard', compact(
            'totalCompanies', 'activeCompanies', 'totalUsers', 'usersToday',
            'monthlyRevenue', 'revenueGrowth', 'activeSubscriptions', 'expiringSoon',
            'recentCompanies', 'recentActivities'
        ));
    }

    public function companies()
    {
        $companies = Company::with(['plan', 'subscription', 'users'])
            ->paginate(20);

        return view('super-admin.companies.index', compact('companies'));
    }

    public function showCompany(Company $company)
    {
        $company->load(['plan', 'subscription', 'users', 'products', 'categories', 'suppliers']);
        
        return view('super-admin.companies.show', compact('company'));
    }

    public function plans()
    {
        $plans = Plan::withCount(['companies', 'subscriptions'])->get();
        
        return view('super-admin.plans.index', compact('plans'));
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with(['company', 'plan'])
            ->latest()
            ->paginate(20);

        return view('super-admin.subscriptions.index', compact('subscriptions'));
    }

    public function analytics()
    {
        // Dados para gráficos e relatórios
        $monthlyStats = [];
        $planStats = Plan::withCount('subscriptions')->get();
        $companyGrowth = Company::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->take(30)
            ->get();

        return view('super-admin.analytics', compact('monthlyStats', 'planStats', 'companyGrowth'));
    }

    public function users()
    {
        $users = User::with(['company'])
            ->paginate(20);

        return view('super-admin.users.index', compact('users'));
    }

    public function impersonate(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->withErrors(['message' => 'Não é possível personificar outro super admin.']);
        }

        session(['impersonating' => $user->id]);
        
        return redirect()->route('dashboard')->with('success', 'Agora você está personificando ' . $user->name);
    }

    public function stopImpersonating()
    {
        session()->forget('impersonating');
        
        return redirect()->route('super-admin.dashboard')->with('success', 'Personificação encerrada.');
    }
}
