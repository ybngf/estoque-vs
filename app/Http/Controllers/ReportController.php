<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view reports');
    }

    /**
     * Display the reports dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        // Stock summary
        $stockSummary = Product::where('company_id', $companyId)
            ->select(
                DB::raw('COUNT(*) as total_products'),
                DB::raw('SUM(stock) as total_stock'),
                DB::raw('SUM(CASE WHEN stock <= min_stock THEN 1 ELSE 0 END) as low_stock_products'),
                DB::raw('SUM(cost_price * stock) as total_cost_value'),
                DB::raw('SUM(sale_price * stock) as total_sale_value')
            )
            ->first();

        // Recent movements (last 30 days)
        $recentMovements = StockMovement::with(['product', 'user'])
            ->whereHas('product', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Movement statistics by type
        $movementStats = StockMovement::whereHas('product', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select('movement_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('movement_type')
            ->get();

        // Top products by movement
        $topProducts = StockMovement::with('product')
            ->whereHas('product', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select('product_id', DB::raw('SUM(ABS(quantity)) as total_moved'))
            ->groupBy('product_id')
            ->orderBy('total_moved', 'desc')
            ->limit(10)
            ->get();

        // Categories with product count
        $categoriesStats = Category::where('company_id', $companyId)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('company_id', $companyId)
            ->whereRaw('stock <= min_stock')
            ->with('category')
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'stockSummary',
            'recentMovements',
            'movementStats',
            'topProducts',
            'categoriesStats',
            'lowStockProducts'
        ));
    }

    /**
     * Generate stock report
     */
    public function stock(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        $query = Product::where('company_id', $companyId)->with(['category', 'supplier']);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereRaw('stock <= min_stock');
                    break;
                case 'zero':
                    $query->where('stock', 0);
                    break;
                case 'negative':
                    $query->where('stock', '<', 0);
                    break;
            }
        }

        $products = $query->orderBy('name')->paginate(50);

        $categories = Category::where('company_id', $companyId)->orderBy('name')->get();
        $suppliers = Supplier::where('company_id', $companyId)->orderBy('name')->get();

        return view('reports.stock', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Generate movements report
     */
    public function movements(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        $query = StockMovement::with(['product', 'user'])
            ->whereHas('product', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });

        // Apply filters
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(50);

        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $users = User::where('company_id', $companyId)->orderBy('name')->get();

        return view('reports.movements', compact('movements', 'products', 'users'));
    }

    /**
     * Export reports to CSV
     */
    public function export(Request $request)
    {
        $this->middleware('permission:export reports');
        
        $type = $request->get('type', 'stock');
        $user = auth()->user();
        $companyId = $user->company_id;

        switch ($type) {
            case 'stock':
                return $this->exportStock($companyId);
            case 'movements':
                return $this->exportMovements($companyId, $request);
            default:
                return redirect()->back()->with('error', 'Tipo de relatório inválido.');
        }
    }

    private function exportStock($companyId)
    {
        $products = Product::where('company_id', $companyId)
            ->with(['category', 'supplier'])
            ->orderBy('name')
            ->get();

        $filename = 'relatorio_estoque_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'Código',
                'Nome',
                'Categoria',
                'Fornecedor',
                'Estoque Atual',
                'Estoque Mínimo',
                'Preço de Custo',
                'Preço de Venda',
                'Valor em Estoque (Custo)',
                'Valor em Estoque (Venda)',
                'Status'
            ], ';');

            foreach ($products as $product) {
                $status = $product->stock <= $product->min_stock ? 'Estoque Baixo' : 'Normal';
                if ($product->stock <= 0) {
                    $status = 'Sem Estoque';
                }

                fputcsv($file, [
                    $product->code ?? '',
                    $product->name,
                    $product->category->name ?? '',
                    $product->supplier->name ?? '',
                    $product->stock,
                    $product->min_stock,
                    number_format($product->cost_price, 2, ',', '.'),
                    number_format($product->sale_price, 2, ',', '.'),
                    number_format($product->cost_price * $product->stock, 2, ',', '.'),
                    number_format($product->sale_price * $product->stock, 2, ',', '.'),
                    $status
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportMovements($companyId, $request)
    {
        $query = StockMovement::with(['product', 'user'])
            ->whereHas('product', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });

        // Apply same filters as movements report
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        $filename = 'relatorio_movimentacoes_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($movements) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'Data',
                'Produto',
                'Tipo',
                'Quantidade',
                'Estoque Anterior',
                'Estoque Atual',
                'Observações',
                'Usuário'
            ], ';');

            foreach ($movements as $movement) {
                $type = match($movement->movement_type) {
                    'entry' => 'Entrada',
                    'exit' => 'Saída',
                    'adjustment' => 'Ajuste',
                    default => $movement->movement_type
                };

                fputcsv($file, [
                    $movement->created_at->format('d/m/Y H:i'),
                    $movement->product->name,
                    $type,
                    $movement->quantity,
                    $movement->previous_stock,
                    $movement->current_stock,
                    $movement->observations ?? '',
                    $movement->user->name ?? ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}