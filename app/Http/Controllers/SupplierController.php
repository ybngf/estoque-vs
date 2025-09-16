<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Traits\HandlesFileUploads;

class SupplierController extends Controller
{
    use HandlesFileUploads;

    public function __construct()
    {
        $this->middleware('permission:view suppliers')->only(['index', 'show']);
        $this->middleware('permission:create suppliers')->only(['create', 'store']);
        $this->middleware('permission:edit suppliers')->only(['edit', 'update']);
        $this->middleware('permission:delete suppliers')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Supplier::withCount('products');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->get('status') === 'active';
            $query->where('is_active', $status);
        }

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();

        // Estatísticas dos fornecedores
        $statistics = [
            'total' => Supplier::count(),
            'active' => Supplier::where('is_active', true)->count(),
            'inactive' => Supplier::where('is_active', false)->count(),
            'with_products' => Supplier::has('products')->count(),
            'without_products' => Supplier::doesntHave('products')->count(),
            'recent' => Supplier::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('suppliers.index', compact('suppliers', 'statistics'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor criado com sucesso!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['products' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Não é possível excluir um fornecedor que possui produtos.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor excluído com sucesso!');
    }
}
