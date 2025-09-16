<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Company;
use App\Events\StockLowEvent;
use App\Events\StockMovementEvent;
use Illuminate\Support\Facades\Event;

class WebhookController extends Controller
{
    /**
     * Webhook para receber notificações de estoque baixo
     */
    public function stockAlert(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'product_id' => 'required|exists:products,id',
            'quantity_on_hand' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
            'webhook_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar token da empresa
            $company = Company::find($request->company_id);
            if (!$this->validateWebhookToken($company, $request->webhook_token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            $product = Product::find($request->product_id);
            
            Log::info('Webhook de estoque baixo processado', [
                'company_id' => $request->company_id,
                'product_id' => $request->product_id,
                'quantity_on_hand' => $request->quantity_on_hand,
                'reorder_point' => $request->reorder_point
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alerta de estoque processado com sucesso',
                'data' => [
                    'product_name' => $product->name,
                    'quantity_on_hand' => $request->quantity_on_hand,
                    'reorder_point' => $request->reorder_point,
                    'status' => 'low_stock_alert_sent'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro no webhook de estoque baixo', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Webhook para movimentações de estoque
     */
    public function stockMovement(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entry,exit,adjustment',
            'quantity' => 'required|integer',
            'reason' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
            'webhook_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar token da empresa
            $company = Company::find($request->company_id);
            if (!$this->validateWebhookToken($company, $request->webhook_token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            $product = Product::find($request->product_id);
            
            // Criar movimentação de estoque
            $movement = StockMovement::create([
                'product_id' => $request->product_id,
                'user_id' => $request->user_id,
                'type' => $request->type,
                'transaction_type' => $request->type === 'entry' ? 'purchase' : ($request->type === 'exit' ? 'sale' : 'adjustment'),
                'quantity_moved' => $request->quantity,
                'quantity_before' => $product->quantity_on_hand,
                'quantity_after' => $this->calculateNewStock($product->quantity_on_hand, $request->type, $request->quantity),
                'unit_cost' => $product->cost_price,
                'total_cost' => $product->cost_price * $request->quantity,
                'memo' => $request->reason ?? 'Movimentação via webhook N8N',
                'company_id' => $request->company_id
            ]);

            // Atualizar estoque do produto
            $newStock = $this->calculateNewStock($product->quantity_on_hand, $request->type, $request->quantity);
            $product->update(['quantity_on_hand' => $newStock]);

            Log::info('Webhook de movimentação processado', [
                'movement_id' => $movement->id,
                'product_id' => $request->product_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'new_stock' => $newStock
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Movimentação processada com sucesso',
                'data' => [
                    'movement_id' => $movement->id,
                    'product_name' => $product->name,
                    'type' => $request->type,
                    'quantity' => $request->quantity,
                    'previous_stock' => $movement->quantity_before,
                    'current_stock' => $newStock
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro no webhook de movimentação', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Gerar token de webhook para uma empresa
     */
    public function generateToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $company = Company::find($request->company_id);
            $token = $this->generateWebhookToken($company);

            return response()->json([
                'success' => true,
                'message' => 'Token gerado com sucesso',
                'data' => [
                    'webhook_token' => $token,
                    'company_id' => $company->id,
                    'company_name' => $company->name,
                    'endpoints' => [
                        'stock_alert' => url('/api/webhooks/stock-alert'),
                        'stock_movement' => url('/api/webhooks/stock-movement'),
                        'product_sync' => url('/api/webhooks/product-sync')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar token de webhook', [
                'error' => $e->getMessage(),
                'company_id' => $request->company_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * Validar token de webhook
     */
    private function validateWebhookToken(Company $company, string $token): bool
    {
        $expectedToken = hash('sha256', $company->id . $company->slug . config('app.key'));
        return hash_equals($expectedToken, $token);
    }

    /**
     * Gerar token de webhook
     */
    private function generateWebhookToken(Company $company): string
    {
        return hash('sha256', $company->id . $company->slug . config('app.key'));
    }

    /**
     * Calcular novo estoque baseado no tipo de movimentação
     */
    private function calculateNewStock(int $currentStock, string $type, int $quantity): int
    {
        switch ($type) {
            case 'entry':
                return $currentStock + $quantity;
            case 'exit':
                return max(0, $currentStock - $quantity);
            case 'adjustment':
                return $quantity;
            default:
                return $currentStock;
        }
    }
}
