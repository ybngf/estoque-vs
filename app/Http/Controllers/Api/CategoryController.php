<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = Category::where('company_id', $companyId);

        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('active')) {
            $query->where('active', $request->get('active') == 'true');
        }

        // Incluir contagem de produtos se solicitado
        if ($request->has('with_products_count') && $request->get('with_products_count') == 'true') {
            $query->withCount('products');
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        if ($request->has('per_page')) {
            $perPage = $request->get('per_page');
            $categories = $query->paginate($perPage);
        } else {
            $categories = $query->get();
        }

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = Category::create(array_merge($request->validated(), [
                'company_id' => $user->company_id,
                'active' => $request->get('active', true)
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Categoria criada com sucesso',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar categoria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se a categoria pertence à empresa do usuário
        if ($category->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $category->loadCount('products');

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se a categoria pertence à empresa do usuário
        if ($category->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Categoria atualizada com sucesso',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar categoria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se a categoria pertence à empresa do usuário
        if ($category->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        // Verificar se existem produtos vinculados
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir categoria com produtos vinculados'
            ], 422);
        }

        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoria excluída com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir categoria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products from this category
     */
    public function products(Request $request, Category $category): JsonResponse
    {
        $user = Auth::user();
        
        // Verificar se a categoria pertence à empresa do usuário
        if ($category->company_id !== $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada'
            ], 404);
        }

        $query = $category->products()->with(['supplier']);

        // Filtros
        if ($request->has('active')) {
            $query->where('active', $request->get('active') == 'true');
        }

        if ($request->has('low_stock') && $request->get('low_stock') == 'true') {
            $query->whereColumn('stock_quantity', '<=', 'minimum_stock');
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
}
