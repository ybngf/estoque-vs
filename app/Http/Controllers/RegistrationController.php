<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class RegistrationController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $plans = Plan::active()->ordered()->get();
        $selectedPlan = null;
        
        if ($request->has('plan')) {
            $selectedPlan = Plan::where('slug', $request->plan)->first();
        }
        
        return view('registration.form', compact('plans', 'selectedPlan'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_document' => ['required', 'string', 'max:20'],
            'company_email' => ['required', 'email', 'max:255', 'unique:companies,email'],
            'company_phone' => ['required', 'string', 'max:20'],
            'company_address' => ['required', 'string', 'max:500'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan_id' => ['required', 'exists:plans,id'],
            'terms' => ['required', 'accepted'],
        ]);

        try {
            DB::beginTransaction();

            // Criar empresa
            $company = Company::create([
                'name' => $request->company_name,
                'slug' => Str::slug($request->company_name) . '-' . Str::random(6),
                'email' => $request->company_email,
                'phone' => $request->company_phone,
                'document' => preg_replace('/\D/', '', $request->company_document),
                'address' => $request->company_address,
                'plan_id' => $request->plan_id,
                'status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
            ]);

            // Criar assinatura trial
            $plan = Plan::find($request->plan_id);
            Subscription::create([
                'company_id' => $company->id,
                'plan_id' => $plan->id,
                'status' => 'trialing',
                'starts_at' => now(),
                'ends_at' => now()->addDays(14),
                'amount' => 0,
                'billing_cycle' => 'monthly'
            ]);

            // Criar usuário admin
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
                'company_id' => $company->id,
                'active' => true,
                'email_verified_at' => now(),
            ]);

            // Atribuir role de admin
            $user->assignRole('admin');

            DB::commit();

            // Fazer login automático
            auth()->login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Empresa cadastrada com sucesso! Você tem 14 dias de teste grátis.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar empresa: ' . $e->getMessage()]);
        }
    }

    public function checkAvailability(Request $request)
    {
        $type = $request->get('type');
        $value = $request->get('value');
        
        $available = true;
        
        if ($type === 'email') {
            $available = !User::where('email', $value)->exists() && 
                        !Company::where('email', $value)->exists();
        } elseif ($type === 'company_email') {
            $available = !Company::where('email', $value)->exists();
        } elseif ($type === 'document') {
            $available = !Company::where('document', preg_replace('/\D/', '', $value))->exists();
        }
        
        return response()->json(['available' => $available]);
    }
}
