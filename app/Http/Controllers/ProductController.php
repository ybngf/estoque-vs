<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\HandlesFileUploads;

class ProductController extends Controller
{
    use HandlesFileUploads;

    public function __construct()
    {
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only(['create', 'store']);
        $this->middleware('permission:edit products')->only(['edit', 'update']);
        $this->middleware('permission:delete products')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        // Aplicar filtros de busca
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('low_stock')) {
            $query->whereColumn('quantity_on_hand', '<=', 'reorder_point');
        }

        $products = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::active()->get();

        // Estatísticas dos produtos
        $statistics = [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
            'low_stock' => Product::whereColumn('quantity_on_hand', '<=', 'reorder_point')->count(),
            'out_of_stock' => Product::where('quantity_on_hand', 0)->count(),
            'recent' => Product::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('products.index', compact('products', 'categories', 'statistics'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $suppliers = Supplier::active()->get();
        
        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'quantity_on_hand' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'unit_of_measure' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ai_tags' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        // Gerar SKU automático se não fornecido
        if (!$validated['sku']) {
            $validated['sku'] = 'PRD-' . strtoupper(Str::random(8));
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['status'] = $validated['is_active'] ? 'active' : 'inactive';

        // Upload da imagem do produto
        if ($request->hasFile('image')) {
            $uploadResult = $this->uploadFile(
                $request->file('image'),
                'products',
                ['thumbnail' => [300, 300]]
            );
            
            if ($uploadResult['success']) {
                $validated['image'] = $uploadResult['path'];
            }
        }

        $product = Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'stockMovements' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        $suppliers = Supplier::active()->get();
        
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'quantity_on_hand' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'unit_of_measure' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ai_tags' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'remove_image' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['status'] = $validated['is_active'] ? 'active' : 'inactive';

        // Gerenciar imagem do produto
        if ($request->has('remove_image') && $request->remove_image) {
            // Remover imagem atual
            if ($product->image) {
                $this->deleteFile($product->image);
                $validated['image'] = null;
            }
        } elseif ($request->hasFile('image')) {
            // Fazer upload da nova imagem
            $uploadResult = $this->uploadFile(
                $request->file('image'),
                'products',
                ['thumbnail' => [300, 300]]
            );
            
            if ($uploadResult['success']) {
                // Remover imagem anterior se existir
                if ($product->image) {
                    $this->deleteFile($product->image);
                }
                $validated['image'] = $uploadResult['path'];
            }
        }

        // Remover o campo remove_image dos dados validados antes de salvar
        unset($validated['remove_image']);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        if ($product->stockMovements()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Não é possível excluir um produto que possui movimentações.');
        }

        // Remover imagem do produto se existir
        if ($product->image) {
            $this->deleteFile($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto excluído com sucesso!');
    }
}
