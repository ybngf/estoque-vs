<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Http\Requests\PlanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::query()->withCount(['companies', 'subscriptions']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $plans = $query->paginate(15);

        // Estatísticas
        $stats = [
            'total' => Plan::count(),
            'active' => Plan::where('active', true)->count(),
            'inactive' => Plan::where('active', false)->count(),
            'with_subscriptions' => Plan::whereHas('subscriptions')->count(),
            'total_revenue' => Subscription::where('status', 'active')->sum('amount')
        ];

        return view('super-admin.plans.index', compact('plans', 'stats'));
    }

    public function show(Plan $plan)
    {
        $plan->load(['companies', 'subscriptions.company']);
        
        return view('super-admin.plans.show', compact('plan'));
    }

    public function create()
    {
        return view('super-admin.plans.create');
    }

    public function store(PlanRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'slug' => 'required|string|max:255|unique:plans,slug',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'trial_days' => 'nullable|integer|min:0',
            'max_users' => 'nullable|integer|min:1',
            'max_products' => 'nullable|integer|min:1',
            'max_storage_gb' => 'nullable|integer|min:1',
            'has_api_access' => 'boolean',
            'has_priority_support' => 'boolean',
            'has_custom_domain' => 'boolean',
            'has_advanced_reports' => 'boolean',
            'active' => 'boolean',
            'is_popular' => 'boolean',
            'stripe_price_id' => 'nullable|string'
        ]);

        try {
            $plan = Plan::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'features' => json_encode($validated['features'] ?? []),
                'price' => $validated['price'],
                'currency' => $validated['currency'],
                'billing_cycle' => $validated['billing_cycle'],
                'trial_days' => $validated['trial_days'],
                'limits' => json_encode([
                    'max_users' => $validated['max_users'],
                    'max_products' => $validated['max_products'],
                    'max_storage_gb' => $validated['max_storage_gb'],
                    'has_api_access' => $validated['has_api_access'] ?? false,
                    'has_priority_support' => $validated['has_priority_support'] ?? false,
                    'has_custom_domain' => $validated['has_custom_domain'] ?? false,
                    'has_advanced_reports' => $validated['has_advanced_reports'] ?? false,
                ]),
                'active' => $validated['active'] ?? true,
                'is_popular' => $validated['is_popular'] ?? false,
                'stripe_price_id' => $validated['stripe_price_id']
            ]);

            return redirect()->route('super-admin.plans.index')
                           ->with('success', 'Plano criado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Erro ao criar plano: ' . $e->getMessage()]);
        }
    }

    public function edit(Plan $plan)
    {
        return view('super-admin.plans.create', compact('plan'));
    }

    public function update(PlanRequest $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('plans')->ignore($plan->id)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('plans')->ignore($plan->id)],
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'trial_days' => 'nullable|integer|min:0',
            'max_users' => 'nullable|integer|min:1',
            'max_products' => 'nullable|integer|min:1',
            'max_storage_gb' => 'nullable|integer|min:1',
            'has_api_access' => 'boolean',
            'has_priority_support' => 'boolean',
            'has_custom_domain' => 'boolean',
            'has_advanced_reports' => 'boolean',
            'active' => 'boolean',
            'is_popular' => 'boolean',
            'stripe_price_id' => 'nullable|string'
        ]);

        try {
            $plan->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'features' => json_encode($validated['features'] ?? []),
                'price' => $validated['price'],
                'currency' => $validated['currency'],
                'billing_cycle' => $validated['billing_cycle'],
                'trial_days' => $validated['trial_days'],
                'limits' => json_encode([
                    'max_users' => $validated['max_users'],
                    'max_products' => $validated['max_products'],
                    'max_storage_gb' => $validated['max_storage_gb'],
                    'has_api_access' => $validated['has_api_access'] ?? false,
                    'has_priority_support' => $validated['has_priority_support'] ?? false,
                    'has_custom_domain' => $validated['has_custom_domain'] ?? false,
                    'has_advanced_reports' => $validated['has_advanced_reports'] ?? false,
                ]),
                'active' => $validated['active'] ?? true,
                'is_popular' => $validated['is_popular'] ?? false,
                'stripe_price_id' => $validated['stripe_price_id']
            ]);

            return redirect()->route('super-admin.plans.show', $plan)
                           ->with('success', 'Plano atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Erro ao atualizar plano: ' . $e->getMessage()]);
        }
    }

    public function destroy(Plan $plan)
    {
        try {
            // Verificar se há assinaturas ativas associadas
            if ($plan->subscriptions()->where('status', 'active')->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir um plano com assinaturas ativas.'
                ], 422);
            }

            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Plano excluído com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir plano: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Plan $plan)
    {
        try {
            $plan->update(['active' => !$plan->active]);

            return response()->json([
                'success' => true,
                'message' => $plan->active ? 'Plano ativado com sucesso!' : 'Plano desativado com sucesso!',
                'active' => $plan->active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status do plano: ' . $e->getMessage()
            ], 500);
        }
    }

    public function duplicate(Plan $plan)
    {
        try {
            $newPlan = $plan->replicate();
            $newPlan->name = $plan->name . ' (Cópia)';
            $newPlan->slug = $plan->slug . '-copy-' . time();
            $newPlan->active = false;
            $newPlan->is_popular = false;
            $newPlan->stripe_price_id = null;
            $newPlan->save();

            return response()->json([
                'success' => true,
                'message' => 'Plano duplicado com sucesso!',
                'redirect' => route('super-admin.plans.edit', $newPlan)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao duplicar plano: ' . $e->getMessage()
            ], 500);
        }
    }
}