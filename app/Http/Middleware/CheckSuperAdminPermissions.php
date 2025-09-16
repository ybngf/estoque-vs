<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdminPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resource = null): Response
    {
        $user = $request->user();
        
        // Verificar se é super admin
        if (!$user || !$user->hasRole('super-admin')) {
            abort(403, 'Acesso negado. Apenas super administradores podem acessar este recurso.');
        }

        // Verificações específicas por recurso
        if ($resource) {
            switch ($resource) {
                case 'users':
                    if (!$user->can('manage-users')) {
                        abort(403, 'Você não tem permissão para gerenciar usuários.');
                    }
                    break;
                    
                case 'plans':
                    if (!$user->can('manage-plans')) {
                        abort(403, 'Você não tem permissão para gerenciar planos.');
                    }
                    break;
                    
                case 'subscriptions':
                    if (!$user->can('manage-subscriptions')) {
                        abort(403, 'Você não tem permissão para gerenciar assinaturas.');
                    }
                    break;
                    
                case 'companies':
                    if (!$user->can('manage-companies')) {
                        abort(403, 'Você não tem permissão para gerenciar empresas.');
                    }
                    break;
                    
                case 'financial':
                    if (!$user->can('view-financial-data')) {
                        abort(403, 'Você não tem permissão para acessar dados financeiros.');
                    }
                    break;
            }
        }

        return $next($request);
    }
}