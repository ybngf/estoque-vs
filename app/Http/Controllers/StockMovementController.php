<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view stock_movements')->only(['index', 'show']);
        $this->middleware('permission:create stock_movements')->only(['create', 'store']);
        $this->middleware('permission:edit stock_movements')->only(['edit', 'update']);
        $this->middleware('permission:delete stock_movements')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        // Filtros
        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('stock-movements.index', compact('movements', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $selectedProduct = null;

        if ($request->filled('product')) {
            $selectedProduct = Product::find($request->product);
        }

        return view('stock-movements.create', compact('products', 'selectedProduct'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'reference' => 'nullable|string|max:100'
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::findOrFail($request->product_id);

            // Validar se há estoque suficiente para saídas
            if ($request->type === 'out' && $product->quantity_on_hand < $request->quantity) {
                throw new \Exception('Estoque insuficiente. Disponível: ' . $product->quantity_on_hand);
            }

            // Determinar transaction_type baseado no type
            $transactionType = match($request->type) {
                'in' => 'purchase',
                'out' => 'sale',
                'adjustment' => 'adjustment',
                default => 'adjustment'
            };

            // Calcular novo estoque
            $previousStock = $product->quantity_on_hand;
            $newStock = $previousStock;
            
            switch ($request->type) {
                case 'in':
                    $newStock += $request->quantity;
                    break;
                case 'out':
                    $newStock -= $request->quantity;
                    break;
                case 'adjustment':
                    $newStock = $request->quantity; // Para ajustes, a quantidade é o novo valor total
                    break;
            }

            // Criar movimentação
            $movement = StockMovement::create([
                'product_id' => $request->product_id,
                'user_id' => Auth::id(),
                'type' => $request->type,
                'transaction_type' => $transactionType,
                'quantity_moved' => $request->quantity,
                'quantity_before' => $previousStock,
                'quantity_after' => $newStock,
                'unit_cost' => $request->unit_cost ?? $product->cost_price,
                'total_cost' => ($request->unit_cost ?? $product->cost_price) * $request->quantity,
                'memo' => $request->notes,
                'reference_number' => $request->reference,
                'company_id' => auth()->user()->company_id
            ]);

            $product->update(['quantity_on_hand' => $newStock]);
        });

        return redirect()->route('stock-movements.index')
            ->with('success', 'Movimentação registrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['product', 'user']);
        return view('stock-movements.show', compact('stockMovement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockMovement $stockMovement)
    {
        // Movimentações só podem ser editadas se foram criadas hoje
        if ($stockMovement->created_at->isToday() && Auth::user()->can('edit stock_movements')) {
            $products = Product::where('active', true)->orderBy('name')->get();
            return view('stock-movements.edit', compact('stockMovement', 'products'));
        }

        return redirect()->route('stock-movements.index')
            ->with('error', 'Movimentações antigas não podem ser editadas.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        // Verificar se pode editar
        if (!$stockMovement->created_at->isToday()) {
            return redirect()->route('stock-movements.index')
                ->with('error', 'Movimentações antigas não podem ser editadas.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
            'reference' => 'nullable|string|max:100'
        ]);

        // Apenas permitir edição de observações e referência
        $stockMovement->update([
            'notes' => $request->notes,
            'reference' => $request->reference
        ]);

        return redirect()->route('stock-movements.index')
            ->with('success', 'Movimentação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        // Movimentações só podem ser excluídas se foram criadas hoje
        if (!$stockMovement->created_at->isToday()) {
            return redirect()->route('stock-movements.index')
                ->with('error', 'Movimentações antigas não podem ser excluídas.');
        }

        DB::transaction(function () use ($stockMovement) {
            $product = $stockMovement->product;
            
            // Reverter o estoque
            $newStock = $product->current_stock;
            
            switch ($stockMovement->type) {
                case 'in':
                    $newStock -= $stockMovement->quantity;
                    break;
                case 'out':
                    $newStock += $stockMovement->quantity;
                    break;
                case 'adjustment':
                    $newStock = $stockMovement->previous_stock;
                    break;
            }

            $product->update(['current_stock' => max(0, $newStock)]);
            $stockMovement->delete();
        });

        return redirect()->route('stock-movements.index')
            ->with('success', 'Movimentação excluída e estoque revertido com sucesso!');
    }

    /**
     * Get product stock information via AJAX
     */
    public function getProductStock($productId)
    {
        $product = Product::findOrFail($productId);
        
        return response()->json([
            'current_stock' => $product->current_stock,
            'minimum_stock' => $product->minimum_stock,
            'unit' => $product->unit ?? 'UN',
            'cost_price' => $product->cost_price
        ]);
    }
}
