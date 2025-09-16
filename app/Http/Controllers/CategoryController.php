<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Traits\HandlesFileUploads;

class CategoryController extends Controller
{
    use HandlesFileUploads;

    public function __construct()
    {
        $this->middleware('permission:view categories')->only(['index', 'show']);
        $this->middleware('permission:create categories')->only(['create', 'store']);
        $this->middleware('permission:edit categories')->only(['edit', 'update']);
        $this->middleware('permission:delete categories')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        // Filtro de busca
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro de status
        if ($request->filled('status')) {
            $status = $request->get('status') === 'active';
            $query->where('is_active', $status);
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        // Estatísticas das categorias
        $statistics = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
            'with_products' => Category::has('products')->count(),
            'without_products' => Category::doesntHave('products')->count(),
            'recent' => Category::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('categories.index', compact('categories', 'statistics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres.'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['products' => function($query) {
            $query->latest()->take(10);
        }]);

        $stats = [
            'total_products' => $category->products()->count(),
            'total_stock' => $category->products()->sum('quantity_on_hand'),
            'total_value' => $category->products()->sum(\DB::raw('quantity_on_hand * cost_price')),
            'low_stock_products' => $category->products()->whereColumn('quantity_on_hand', '<=', 'reorder_point')->count()
        ];

        return view('categories.show', compact('category', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'O nome da categoria é obrigatório.',
            'name.unique' => 'Já existe uma categoria com este nome.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'description.max' => 'A descrição deve ter no máximo 1000 caracteres.'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Verificar se a categoria tem produtos
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Não é possível excluir uma categoria que possui produtos.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
