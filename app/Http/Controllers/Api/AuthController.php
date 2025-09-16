<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login do usuário e geração de token
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        if (!$user->active) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário inativo'
            ], 401);
        }

        // Atualizar último login
        $user->update(['last_login' => now()]);

        // Gerar token
        $deviceName = $request->device_name ?? 'api-client';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => $user->load(['company', 'roles']),
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Informações do usuário autenticado
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['company', 'roles']);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Revogar token atual
        $request->user()->currentAccessToken()->delete();
        
        // Gerar novo token
        $deviceName = $request->device_name ?? 'api-client';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token renovado com sucesso',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Listar tokens ativos do usuário
     */
    public function tokens(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens;

        return response()->json([
            'success' => true,
            'data' => $tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at
                ];
            })
        ]);
    }

    /**
     * Revogar token específico
     */
    public function revokeToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Token ID é obrigatório',
                'errors' => $validator->errors()
            ], 422);
        }

        $token = $request->user()->tokens()->find($request->token_id);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não encontrado'
            ], 404);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revogado com sucesso'
        ]);
    }

    /**
     * Revogar todos os tokens do usuário
     */
    public function revokeAllTokens(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todos os tokens foram revogados'
        ]);
    }
}
