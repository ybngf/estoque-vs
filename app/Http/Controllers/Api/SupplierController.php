<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    /**
     * Display a listing of the suppliers
     */
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::where('company_id', auth()->user()->company_id);
        
        // Search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Sort
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->input('per_page', 15);
        $suppliers = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $suppliers
        ]);
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'contact_person' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'status' => 'required|in:active,inactive'
            ]);
            
            $validated['company_id'] = auth()->user()->company_id;
            
            $supplier = Supplier::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Fornecedor criado com sucesso',
                'data' => $supplier
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Erro inesperado'
            ], 500);
        }
    }

    /**
     * Display the specified supplier
     */
    public function show(string $id): JsonResponse
    {
        try {
            $supplier = Supplier::where('company_id', auth()->user()->company_id)
                               ->findOrFail($id);
            
            // Load related products count
            $supplier->loadCount('products');
            
            return response()->json([
                'success' => true,
                'data' => $supplier
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $supplier = Supplier::where('company_id', auth()->user()->company_id)
                               ->findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'contact_person' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
                'status' => 'required|in:active,inactive'
            ]);
            
            $supplier->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Fornecedor atualizado com sucesso',
                'data' => $supplier
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
                'message' => config('app.debug') ? $e->getMessage() : 'Fornecedor não encontrado'
            ], 404);
        }
    }

    /**
     * Remove the specified supplier
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $supplier = Supplier::where('company_id', auth()->user()->company_id)
                               ->findOrFail($id);
            
            // Check if supplier has products
            $productsCount = $supplier->products()->count();
            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Não é possível excluir. Fornecedor possui {$productsCount} produto(s) associado(s)."
                ], 400);
            }
            
            $supplier->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Fornecedor excluído com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ], 404);
        }
    }

    /**
     * Get supplier products
     */
    public function products(Request $request, string $id): JsonResponse
    {
        try {
            $supplier = Supplier::where('company_id', auth()->user()->company_id)
                               ->findOrFail($id);
            
            $query = $supplier->products();
            
            // Search filter
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            }
            
            // Sort
            $sortBy = $request->input('sort_by', 'name');
            $sortOrder = $request->input('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Pagination
            $perPage = $request->input('per_page', 15);
            $products = $query->with('category')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'supplier' => $supplier
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ], 404);
        }
    }

    /**
     * Get supplier statistics
     */
    public function stats(string $id): JsonResponse
    {
        try {
            $supplier = Supplier::where('company_id', auth()->user()->company_id)
                               ->findOrFail($id);
            
            $stats = [
                'total_products' => $supplier->products()->count(),
                'active_products' => $supplier->products()->where('status', 'active')->count(),
                'inactive_products' => $supplier->products()->where('status', 'inactive')->count(),
                'total_stock_value' => $supplier->products()->sum(\DB::raw('current_stock * price')),
                'low_stock_products' => $supplier->products()
                    ->whereRaw('current_stock <= min_stock')
                    ->where('current_stock', '>', 0)
                    ->count(),
                'out_of_stock_products' => $supplier->products()
                    ->where('current_stock', '<=', 0)
                    ->count()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'supplier' => $supplier
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fornecedor não encontrado'
            ], 404);
        }
    }
}
