<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = Product::where('company_id', $companyId)
                       ->with(['category', 'supplier']);

        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->get('supplier_id'));
        }

        if ($request->has('low_stock') && $request->get('low_stock') == 'true') {
            $query->whereColumn('quantity_on_hand', '<=', 'reorder_point');
        }

        if ($request->has('out_of_stock') && $request->get('out_of_stock') == 'true') {
            $query->where('quantity_on_hand', 0);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->get('is_active') == 'true');
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'quantity_on_hand' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'unit_of_measure' => 'required|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $productData = $request->all();
            $productData['company_id'] = $user->company_id;
            $productData['is_active'] = $request->get('is_active', true);
            $productData['status'] = $productData['is_active'] ? 'active' : 'inactive';

            $product = Product::create($productData);

            // Criar movimentação inicial de estoque
            if ($product->quantity_on_hand > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'entry',
                    'transaction_type' => 'adjustment',
                    'quantity_moved' => $product->quantity_on_hand,
                    'quantity_before' => 0,
                    'quantity_after' => $product->quantity_on_hand,
                    'unit_cost' => $product->cost_price,
                    'total_cost' => $product->cost_price * $product->quantity_on_hand,
                    'memo' => 'Estoque inicial do produto',
                    'company_id' => $user->company_id
                ]);
            }

            $product->load(['category', 'supplier']);

            return response()->json([
                'success' => true,
                'message' => 'Produto criado com sucesso',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se o produto pertence à empresa do usuário
        if ($product->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $product->load(['category', 'supplier']);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se o produto pertence à empresa do usuário
        if ($product->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'sku' => 'sometimes|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'name' => 'sometimes|string|max:255',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cost_price' => 'sometimes|numeric|min:0',
            'price' => 'sometimes|numeric|min:0',
            'reorder_point' => 'sometimes|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'unit_of_measure' => 'sometimes|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->all();
            if (isset($updateData['is_active'])) {
                $updateData['status'] = $updateData['is_active'] ? 'active' : 'inactive';
            }
            
            $product->update($updateData);
            $product->load(['category', 'supplier']);

            return response()->json([
                'success' => true,
                'message' => 'Produto atualizado com sucesso',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se o produto pertence à empresa do usuário
        if ($product->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produto excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir produto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product movements
     */
    public function movements(Product $product): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se o produto pertence à empresa do usuário
        if ($product->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $movements = $product->stockMovements()
                           ->with('user')
                           ->latest()
                           ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $movements
        ]);
    }

    /**
     * Adjust product stock
     */
    public function adjustStock(Request $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se o produto pertence à empresa do usuário
        if ($product->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:entry,exit,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $previousStock = $product->quantity_on_hand;
            
            // Calcular novo estoque
            switch ($request->type) {
                case 'entry':
                    $newStock = $previousStock + $request->quantity;
                    $transactionType = 'purchase';
                    break;
                case 'exit':
                    $newStock = max(0, $previousStock - $request->quantity);
                    $transactionType = 'sale';
                    break;
                case 'adjustment':
                    $newStock = $request->quantity;
                    $transactionType = 'adjustment';
                    break;
            }

            // Criar movimentação
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => $request->type,
                'transaction_type' => $transactionType,
                'quantity_moved' => $request->quantity,
                'quantity_before' => $previousStock,
                'quantity_after' => $newStock,
                'unit_cost' => $product->cost_price,
                'total_cost' => $product->cost_price * $request->quantity,
                'memo' => $request->reason,
                'company_id' => $user->company_id
            ]);

            // Atualizar estoque do produto
            $product->update(['quantity_on_hand' => $newStock]);

            return response()->json([
                'success' => true,
                'message' => 'Estoque ajustado com sucesso',
                'data' => [
                    'movement' => $movement,
                    'previous_stock' => $previousStock,
                    'current_stock' => $newStock,
                    'product' => $product->fresh()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao ajustar estoque',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
