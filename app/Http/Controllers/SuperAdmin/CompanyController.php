<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query()->with(['users', 'plan', 'subscription']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $companies = $query->paginate(15);

        // Estatísticas
        $stats = [
            'total' => Company::count(),
            'active' => Company::where('status', 'active')->count(),
            'inactive' => Company::where('status', 'inactive')->count(),
            'with_subscription' => Company::whereHas('subscription', function($q) {
                $q->where('status', 'active');
            })->count(),
            'trial' => Company::where('status', 'trial')->count(),
            'monthly_revenue' => Subscription::where('status', 'active')->sum('amount')
        ];

        $plans = Plan::all();

        return view('super-admin.companies.index', compact('companies', 'stats', 'plans'));
    }

    public function show(Company $company)
    {
        $company->load(['users', 'plan', 'subscription', 'products', 'categories', 'suppliers']);
        
        return view('super-admin.companies.show', compact('company'));
    }

    public function create()
    {
        $plans = Plan::all();
        return view('super-admin.companies.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:companies,email',
            'document' => 'nullable|string|max:18|unique:companies,document',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'plan_id' => 'nullable|exists:plans,id',
            'status' => 'nullable|in:trial,active,inactive,suspended',
            'trial_ends_at' => 'nullable|date',
            'subscription_amount' => 'nullable|numeric|min:0',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed'
        ]);

        DB::beginTransaction();

        try {
            // Criar empresa
            $company = Company::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'document' => $validated['document'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'plan_id' => $validated['plan_id'],
                'status' => $validated['status'] ?? 'trial'
            ]);

            // Criar usuário administrador
            $admin = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'company_id' => $company->id,
                'active' => true
            ]);

            // Atribuir role de administrador
            $admin->assignRole('admin');

            // Criar assinatura se especificada
            if ($validated['status'] && $validated['status'] !== 'trial') {
                Subscription::create([
                    'company_id' => $company->id,
                    'user_id' => $admin->id,
                    'plan_id' => $validated['plan_id'],
                    'status' => $validated['status'],
                    'amount' => $validated['subscription_amount'],
                    'starts_at' => now(),
                    'ends_at' => $validated['trial_ends_at'] ? $validated['trial_ends_at'] : null
                ]);
            }

            DB::commit();

            return redirect()->route('super-admin.companies.index')
                           ->with('success', 'Empresa criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Erro ao criar empresa: ' . $e->getMessage()]);
        }
    }

    public function edit(Company $company)
    {
        $plans = Plan::all();
        return view('super-admin.companies.create', compact('company', 'plans'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('companies')->ignore($company->id)],
            'document' => ['nullable', 'string', 'max:18', Rule::unique('companies')->ignore($company->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'plan_id' => 'nullable|exists:plans,id',
            'status' => 'nullable|in:trial,active,inactive,suspended',
            'trial_ends_at' => 'nullable|date',
            'subscription_amount' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();

        try {
            // Atualizar empresa
            $company->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'document' => $validated['document'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'plan_id' => $validated['plan_id'],
                'status' => $validated['status'] ?? 'trial'
            ]);

            // Atualizar ou criar assinatura
            if ($validated['status'] && $validated['status'] !== 'trial') {
                $subscription = $company->subscription ?? new Subscription();
                
                $subscription->fill([
                    'company_id' => $company->id,
                    'plan_id' => $validated['plan_id'],
                    'status' => $validated['status'],
                    'amount' => $validated['subscription_amount'],
                    'ends_at' => $validated['trial_ends_at']
                ]);

                if (!$subscription->exists) {
                    $subscription->user_id = $company->users->first()->id ?? null;
                    $subscription->starts_at = now();
                }

                $subscription->save();
            }

            DB::commit();

            return redirect()->route('super-admin.companies.show', $company)
                           ->with('success', 'Empresa atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Erro ao atualizar empresa: ' . $e->getMessage()]);
        }
    }

    public function destroy(Company $company)
    {
        try {
            // Verificar se há usuários associados
            if ($company->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir uma empresa com usuários associados.'
                ], 422);
            }

            $company->delete();

            return response()->json([
                'success' => true,
                'message' => 'Empresa excluída com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Company $company)
    {
        try {
            $newStatus = $company->status === 'active' ? 'inactive' : 'active';
            $company->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => $newStatus === 'active' ? 'Empresa ativada com sucesso!' : 'Empresa desativada com sucesso!',
                'status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status da empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $query = Company::query()->with(['users', 'plan', 'subscription']);

        // Aplicar mesmos filtros da listagem
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        $companies = $query->get();

        $filename = 'empresas_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($companies) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalhos
            fputcsv($file, [
                'ID',
                'Nome',
                'Email',
                'Documento',
                'Telefone',
                'Plano',
                'Status',
                'Valor Assinatura',
                'Usuários',
                'Criada em'
            ]);

            // Dados
            foreach ($companies as $company) {
                fputcsv($file, [
                    $company->id,
                    $company->name,
                    $company->email,
                    $company->document,
                    $company->phone,
                    $company->plan ? $company->plan->name : 'Sem plano',
                    ucfirst($company->status),
                    $company->subscription ? number_format($company->subscription->amount, 2, ',', '.') : '0,00',
                    $company->users->count(),
                    $company->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}