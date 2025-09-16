<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Super admin pode acessar qualquer coisa sem verificações de empresa ou assinatura
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Verificar se o usuário tem uma empresa associada
        if (!$user || !$user->company_id) {
            return redirect()->route('login')->withErrors([
                'message' => 'Usuário deve estar associado a uma empresa.'
            ]);
        }

        // Verificar se a empresa do usuário está ativa
        if (!$user->company || !$user->company->isActive()) {
            return redirect()->route('login')->withErrors([
                'message' => 'Empresa inativa. Entre em contato com o suporte.'
            ]);
        }

        // Verificar se a assinatura da empresa está ativa (somente para usuários não super admin)
        $subscription = $user->company->subscription;
        if (!$subscription || (!$subscription->isActive() && !$subscription->isTrial())) {
            return redirect()->route('subscription.expired')->withErrors([
                'message' => 'Assinatura expirada. Renove para continuar usando o sistema.'
            ]);
        }

        // Adicionar a empresa do usuário no contexto da requisição
        $request->merge(['company_id' => $user->company_id]);

        return $next($request);
    }
}
