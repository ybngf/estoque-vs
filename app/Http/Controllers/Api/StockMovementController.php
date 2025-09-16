<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the stock movements
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockMovement::whereHas('product', function ($q) {
            $q->where('company_id', auth()->user()->company_id);
        });
        
        // Date range filter
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }
        
        // Product filter
        if ($request->has('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }
        
        // Movement type filter
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }
        
        // User filter
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        
        // Search filter (product name, SKU, or barcode)
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->input('per_page', 15);
        $movements = $query->with(['product', 'user'])->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $movements
        ]);
    }

    /**
     * Store a newly created stock movement
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'type' => 'required|in:entry,exit,adjustment',
                'quantity' => 'required|numeric|min:0.01',
                'unit_cost' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
                'reference' => 'nullable|string|max:100'
            ]);
            
            // Verify product belongs to user's company
            $product = Product::where('id', $validated['product_id'])
                             ->where('company_id', auth()->user()->company_id)
                             ->firstOrFail();
            
            DB::beginTransaction();
            
            try {
                // Create the movement
                $movement = new StockMovement($validated);
                $movement->user_id = auth()->id();
                $movement->previous_stock = $product->current_stock;
                
                // Calculate new stock based on movement type
                switch ($validated['type']) {
                    case 'entry':
                        $newStock = $product->current_stock + $validated['quantity'];
                        break;
                    case 'exit':
                        $newStock = $product->current_stock - $validated['quantity'];
                        if ($newStock < 0) {
                            throw new \Exception('Estoque insuficiente para esta saída');
                        }
                        break;
                    case 'adjustment':
                        $newStock = $validated['quantity'];
                        break;
                }
                
                $movement->new_stock = $newStock;
                $movement->save();
                
                // Update product stock
                $product->current_stock = $newStock;
                
                // Update average cost if unit_cost is provided and it's an entry
                if ($validated['type'] === 'entry' && isset($validated['unit_cost'])) {
                    if ($product->current_stock > 0) {
                        $totalValue = ($product->current_stock * $product->cost_price) + 
                                     ($validated['quantity'] * $validated['unit_cost']);
                        $totalQuantity = $product->current_stock + $validated['quantity'];
                        $product->cost_price = $totalValue / $totalQuantity;
                    } else {
                        $product->cost_price = $validated['unit_cost'];
                    }
                }
                
                $product->save();
                
                DB::commit();
                
                // Load relationships for response
                $movement->load(['product', 'user']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Movimentação registrada com sucesso',
                    'data' => $movement
                ], 201);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified stock movement
     */
    public function show(string $id): JsonResponse
    {
        try {
            $movement = StockMovement::whereHas('product', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            })->with(['product', 'user'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $movement
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Movimentação não encontrada'
            ], 404);
        }
    }

    /**
     * Update the specified stock movement (limited updates)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $movement = StockMovement::whereHas('product', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            })->findOrFail($id);
            
            // Only allow updating notes and reference
            $validated = $request->validate([
                'notes' => 'nullable|string|max:1000',
                'reference' => 'nullable|string|max:100'
            ]);
            
            $movement->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Movimentação atualizada com sucesso',
                'data' => $movement->load(['product', 'user'])
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Movimentação não encontrada'
            ], 404);
        }
    }

    /**
     * Get movement statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        
        // Date range filter
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());
        
        $query = StockMovement::whereHas('product', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->whereBetween('created_at', [$startDate, $endDate]);
        
        $stats = [
            'total_movements' => $query->count(),
            'entries' => $query->where('type', 'entry')->count(),
            'exits' => $query->where('type', 'exit')->count(),
            'adjustments' => $query->where('type', 'adjustment')->count(),
            'total_entry_quantity' => $query->where('type', 'entry')->sum('quantity'),
            'total_exit_quantity' => $query->where('type', 'exit')->sum('quantity'),
            'total_entry_value' => $query->where('type', 'entry')
                                         ->whereNotNull('unit_cost')
                                         ->sum(DB::raw('quantity * unit_cost')),
            'movements_by_day' => $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                       ->groupBy('date')
                                       ->orderBy('date')
                                       ->get(),
            'movements_by_type' => $query->selectRaw('type, COUNT(*) as count, SUM(quantity) as total_quantity')
                                        ->groupBy('type')
                                        ->get(),
            'top_products' => $query->selectRaw('product_id, COUNT(*) as movement_count, SUM(quantity) as total_quantity')
                                   ->with('product:id,name,sku')
                                   ->groupBy('product_id')
                                   ->orderBy('movement_count', 'desc')
                                   ->limit(10)
                                   ->get()
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Bulk stock adjustment
     */
    public function bulkAdjustment(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'adjustments' => 'required|array',
                'adjustments.*.product_id' => 'required|exists:products,id',
                'adjustments.*.quantity' => 'required|numeric|min:0',
                'adjustments.*.notes' => 'nullable|string|max:1000',
                'reference' => 'nullable|string|max:100'
            ]);
            
            $companyId = auth()->user()->company_id;
            $userId = auth()->id();
            $movements = [];
            
            DB::beginTransaction();
            
            try {
                foreach ($validated['adjustments'] as $adjustment) {
                    // Verify product belongs to user's company
                    $product = Product::where('id', $adjustment['product_id'])
                                     ->where('company_id', $companyId)
                                     ->firstOrFail();
                    
                    // Create movement record
                    $movement = StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'adjustment',
                        'quantity' => $adjustment['quantity'],
                        'previous_stock' => $product->current_stock,
                        'new_stock' => $adjustment['quantity'],
                        'user_id' => $userId,
                        'notes' => $adjustment['notes'] ?? null,
                        'reference' => $validated['reference'] ?? null
                    ]);
                    
                    // Update product stock
                    $product->current_stock = $adjustment['quantity'];
                    $product->save();
                    
                    $movements[] = $movement->load(['product', 'user']);
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Ajuste em lote realizado com sucesso',
                    'data' => $movements
                ], 201);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
