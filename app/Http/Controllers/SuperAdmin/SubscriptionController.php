<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Company;
use App\Http\Requests\SubscriptionRequest;
use App\Traits\LogsAdminActions;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    use LogsAdminActions;

    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['company', 'plan']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('company')) {
            $query->whereHas('company', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->company . '%');
            });
        }

        if ($request->filled('expiring_days')) {
            $days = (int) $request->expiring_days;
            $query->where('ends_at', '<=', now()->addDays($days))
                  ->where('status', '!=', Subscription::STATUS_CANCELED);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', Subscription::STATUS_ACTIVE)->count(),
            'trial' => Subscription::where('status', Subscription::STATUS_TRIALING)->count(),
            'expired' => Subscription::where('ends_at', '<', now())->count(),
            'revenue_monthly' => Subscription::where('status', Subscription::STATUS_ACTIVE)
                                             ->where('billing_cycle', 'monthly')
                                             ->sum('amount'),
            'revenue_yearly' => Subscription::where('status', Subscription::STATUS_ACTIVE)
                                            ->where('billing_cycle', 'yearly')
                                            ->sum('amount')
        ];

        $plans = Plan::where('is_active', true)->get();

        return view('super-admin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $plans = Plan::where('is_active', true)->orderBy('name')->get();

        return view('super-admin.subscriptions.create', compact('companies', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubscriptionRequest $request)
    {
        $validated = $request->validated();

        // Buscar o plano para pegar o preço
        $plan = Plan::findOrFail($validated['plan_id']);
        
        // Definir o valor baseado no ciclo
        $amount = $validated['billing_cycle'] === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

        // Calcular data de término se não fornecida
        if (!isset($validated['ends_at'])) {
            $months = $validated['billing_cycle'] === 'yearly' ? 12 : 1;
            $validated['ends_at'] = Carbon::parse($validated['starts_at'])->addMonths($months);
        }

        // Calcular próxima data de cobrança
        $validated['next_billing_date'] = Carbon::parse($validated['ends_at']);
        $validated['amount'] = $amount;

        $subscription = Subscription::create($validated);

        // Log da criação
        $this->logSubscriptionAction('created', $subscription);

        return redirect()->route('super-admin.subscriptions.index')
                        ->with('success', 'Assinatura criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['company', 'plan']);
        
        return view('super-admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        $companies = Company::orderBy('name')->get();
        $plans = Plan::where('is_active', true)->orderBy('name')->get();

        return view('super-admin.subscriptions.edit', compact('subscription', 'companies', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubscriptionRequest $request, Subscription $subscription)
    {
        $validated = $request->validated();

        // Se o plano mudou, recalcular o valor
        if ($subscription->plan_id != $validated['plan_id']) {
            $plan = Plan::findOrFail($validated['plan_id']);
            $validated['amount'] = $validated['billing_cycle'] === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
        }

        // Calcular data de término se não fornecida
        if (!isset($validated['ends_at'])) {
            $months = $validated['billing_cycle'] === 'yearly' ? 12 : 1;
            $validated['ends_at'] = Carbon::parse($validated['starts_at'])->addMonths($months);
        }

        // Atualizar próxima data de cobrança
        $validated['next_billing_date'] = Carbon::parse($validated['ends_at']);

        // Capturar dados originais para log
        $originalData = $subscription->toArray();
        
        $subscription->update($validated);

        // Log da atualização
        $changes = array_diff_assoc($validated, $originalData);
        $this->logSubscriptionAction('updated', $subscription, $changes);

        return redirect()->route('super-admin.subscriptions.index')
                        ->with('success', 'Assinatura atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('super-admin.subscriptions.index')
                        ->with('success', 'Assinatura excluída com sucesso!');
    }

    /**
     * Renovar assinatura
     */
    public function renew(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'months' => 'required|integer|min:1|max:24',
            'keep_price' => 'boolean'
        ]);

        $months = $validated['months'];
        
        // Se não manter preço, atualizar com preço atual do plano
        if (!($validated['keep_price'] ?? false)) {
            $plan = $subscription->plan;
            $newAmount = $subscription->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;
            $subscription->amount = $newAmount;
        }

        $subscription->renew($months);

        return redirect()->back()->with('success', "Assinatura renovada por {$months} mês(es)!");
    }

    /**
     * Cancelar assinatura
     */
    public function cancel(Subscription $subscription)
    {
        $subscription->cancel();

        return redirect()->back()->with('success', 'Assinatura cancelada com sucesso!');
    }

    /**
     * Reativar assinatura
     */
    public function reactivate(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'months' => 'required|integer|min:1|max:24'
        ]);

        $months = $validated['months'];
        
        $subscription->status = Subscription::STATUS_ACTIVE;
        $subscription->ends_at = now()->addMonths($months);
        $subscription->next_billing_date = $subscription->ends_at;
        $subscription->save();

        return redirect()->back()->with('success', "Assinatura reativada por {$months} mês(es)!");
    }

    /**
     * Alterar plano da assinatura
     */
    public function changePlan(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id|different:' . $subscription->plan_id,
            'prorate' => 'boolean'
        ]);

        $newPlan = Plan::findOrFail($validated['plan_id']);
        
        // Calcular novo valor
        $newAmount = $subscription->billing_cycle === 'yearly' ? 
                    $newPlan->yearly_price : $newPlan->monthly_price;

        // Se for prorata, calcular proporcional
        if ($validated['prorate'] ?? false) {
            $daysRemaining = $subscription->getDaysRemaining();
            $totalDays = $subscription->billing_cycle === 'yearly' ? 365 : 30;
            $proRataAmount = ($newAmount * $daysRemaining) / $totalDays;
            $newAmount = $proRataAmount;
        }

        $subscription->plan_id = $validated['plan_id'];
        $subscription->amount = $newAmount;
        $subscription->save();

        return redirect()->back()->with('success', 'Plano alterado com sucesso!');
    }

    /**
     * Marcar como pago
     */
    public function markAsPaid(Subscription $subscription)
    {
        if ($subscription->status === Subscription::STATUS_PAST_DUE) {
            $subscription->status = Subscription::STATUS_ACTIVE;
            $subscription->save();

            return redirect()->back()->with('success', 'Assinatura marcada como paga!');
        }

        return redirect()->back()->with('error', 'Assinatura não está com pagamento pendente.');
    }
}