<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        // Estatísticas básicas da empresa
        $stats = [
            'total_products' => Product::where('company_id', $companyId)->count(),
            'total_categories' => Category::where('company_id', $companyId)->count(),
            'total_suppliers' => Supplier::where('company_id', $companyId)->count(),
            'total_users' => User::where('company_id', $companyId)->count(),
            'low_stock_products' => Product::where('company_id', $companyId)
                ->whereColumn('quantity_on_hand', '<=', 'reorder_point')->count(),
            'out_of_stock_products' => Product::where('company_id', $companyId)
                ->where('quantity_on_hand', 0)->count(),
            'total_stock_value' => Product::where('company_id', $companyId)
                ->sum(DB::raw('quantity_on_hand * cost_price')),
            'active_products' => Product::where('company_id', $companyId)
                ->where('is_active', true)->count(),
            'recent_products' => Product::where('company_id', $companyId)
                ->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Produtos com estoque baixo
        $lowStockProducts = Product::with('category')
            ->where('company_id', $companyId)
            ->whereColumn('quantity_on_hand', '<=', 'reorder_point')
            ->orderBy('quantity_on_hand', 'asc')
            ->limit(5)
            ->get();

        // Movimentações recentes
        $recentMovements = StockMovement::with(['product', 'user'])
            ->whereHas('product', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->latest()
            ->limit(5)
            ->get();

        // Produtos mais vendidos (saídas)
        $topProducts = StockMovement::select('product_id', DB::raw('SUM(quantity_moved) as total_sold'))
            ->where('type', 'exit')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereHas('product', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->with('product')
            ->limit(5)
            ->get();

        // Dados para gráficos
        $movementsByMonth = StockMovement::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN type = "entry" THEN quantity_moved ELSE 0 END) as entries'),
                DB::raw('SUM(CASE WHEN type = "exit" THEN quantity_moved ELSE 0 END) as exits')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->whereHas('product', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Valor do estoque por categoria
        $stockByCategory = Category::select('categories.name')
            ->selectRaw('SUM(products.quantity_on_hand * products.cost_price) as total_value')
            ->leftJoin('products', function($join) use ($companyId) {
                $join->on('categories.id', '=', 'products.category_id')
                     ->where('products.company_id', '=', $companyId);
            })
            ->where('categories.company_id', $companyId)
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_value', 'desc')
            ->get();

        // Usuários ativos da empresa
        $activeUsers = User::where('company_id', $companyId)
            ->where('active', true)
            ->orderBy('last_login_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'lowStockProducts',
            'recentMovements',
            'topProducts',
            'movementsByMonth',
            'stockByCategory',
            'activeUsers'
        ));
    }
}
