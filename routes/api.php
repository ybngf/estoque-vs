<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\BackupController;
use App\Http\Controllers\Api\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('tokens', [AuthController::class, 'tokens']);
        Route::delete('tokens/{token_id}', [AuthController::class, 'revokeToken']);
        Route::delete('tokens', [AuthController::class, 'revokeAllTokens']);
    });
});

// Webhook Routes (sem autenticação para receber de sistemas externos)
Route::prefix('webhooks')->group(function () {
    Route::post('stock-alert', [WebhookController::class, 'stockAlert']);
    Route::post('stock-movement', [WebhookController::class, 'stockMovement']);
    Route::post('product-sync', [WebhookController::class, 'productSync']);
    Route::post('generate-token', [WebhookController::class, 'generateToken']);
});

// API Routes com autenticação
Route::middleware('auth:sanctum')->group(function () {
    
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user()->load('company');
    });

    // Products API
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{product}', [ProductController::class, 'show']);
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
        Route::get('/{product}/movements', [ProductController::class, 'movements']);
        Route::post('/{product}/adjust-stock', [ProductController::class, 'adjustStock']);
    });

    // Categories API
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
        Route::get('/{category}/products', [CategoryController::class, 'products']);
    });

    // Suppliers API
    Route::prefix('suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/{supplier}', [SupplierController::class, 'show']);
        Route::put('/{supplier}', [SupplierController::class, 'update']);
        Route::delete('/{supplier}', [SupplierController::class, 'destroy']);
        Route::get('/{supplier}/products', [SupplierController::class, 'products']);
    });

    // Stock Movements API
    Route::prefix('stock-movements')->group(function () {
        Route::get('/', [StockMovementController::class, 'index']);
        Route::post('/', [StockMovementController::class, 'store']);
        Route::get('/{movement}', [StockMovementController::class, 'show']);
        Route::put('/{movement}', [StockMovementController::class, 'update']);
        Route::get('/stats', [StockMovementController::class, 'stats']);
        Route::post('/bulk-adjustment', [StockMovementController::class, 'bulkAdjustment']);
    });

    // Notifications API
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::get('/stats', [NotificationController::class, 'stats']);
    });

    // Statistics and Reports
    Route::prefix('stats')->group(function () {
        Route::get('/products/{id}', [ProductController::class, 'stats']);
        Route::get('/categories/{id}', [CategoryController::class, 'stats']);
        Route::get('/suppliers/{id}', [SupplierController::class, 'stats']);
    });

    // Settings API
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::get('/{key}', [SettingsController::class, 'show']);
        Route::post('/', [SettingsController::class, 'store']);
        Route::put('/bulk', [SettingsController::class, 'bulkUpdate']);
        Route::delete('/{key}', [SettingsController::class, 'destroy']);
        Route::post('/reset', [SettingsController::class, 'reset']);
    });

    // Backups API
    Route::prefix('backups')->group(function () {
        Route::get('/', [BackupController::class, 'index']);
        Route::post('/', [BackupController::class, 'store']);
        Route::get('/{id}', [BackupController::class, 'show']);
        Route::delete('/{id}', [BackupController::class, 'destroy']);
        Route::get('/{id}/download', [BackupController::class, 'download']);
        Route::get('/stats', [BackupController::class, 'stats']);
    });

    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('/inventory', function (Request $request) {
            $companyId = auth()->user()->company_id;
            
            $query = \App\Models\Product::where('company_id', $companyId)
                                       ->with(['category', 'supplier']);
            
            // Filters
            if ($request->has('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }
            
            if ($request->has('supplier_id')) {
                $query->where('supplier_id', $request->input('supplier_id'));
            }
            
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }
            
            if ($request->has('low_stock')) {
                $query->whereRaw('current_stock <= min_stock');
            }
            
            if ($request->has('out_of_stock')) {
                $query->where('current_stock', '<=', 0);
            }
            
            $products = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $products,
                'summary' => [
                    'total_products' => $products->count(),
                    'total_stock_value' => $products->sum(function ($product) {
                        return $product->current_stock * $product->price;
                    }),
                    'low_stock_count' => $products->filter(function ($product) {
                        return $product->current_stock <= $product->min_stock;
                    })->count()
                ]
            ]);
        });
        
        Route::get('/movements', function (Request $request) {
            $companyId = auth()->user()->company_id;
            
            $query = \App\Models\StockMovement::whereHas('product', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->with(['product', 'user']);
            
            // Date filters
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->input('start_date'));
            }
            
            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->input('end_date'));
            }
            
            // Type filter
            if ($request->has('type')) {
                $query->where('type', $request->input('type'));
            }
            
            $movements = $query->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $movements,
                'summary' => [
                    'total_movements' => $movements->count(),
                    'entries' => $movements->where('type', 'entry')->count(),
                    'exits' => $movements->where('type', 'exit')->count(),
                    'adjustments' => $movements->where('type', 'adjustment')->count()
                ]
            ]);
        });
        
        Route::get('/analytics', function (Request $request) {
            $companyId = auth()->user()->company_id;
            $startDate = $request->input('start_date', now()->subDays(30));
            $endDate = $request->input('end_date', now());
            
            // Movement trends
            $movementTrends = \App\Models\StockMovement::whereHas('product', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, type, COUNT(*) as count, SUM(quantity) as total_quantity')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();
            
            // Top products by movement
            $topProducts = \App\Models\StockMovement::whereHas('product', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('product_id, COUNT(*) as movement_count, SUM(ABS(quantity)) as total_quantity')
            ->with('product:id,name,sku')
            ->groupBy('product_id')
            ->orderBy('movement_count', 'desc')
            ->limit(10)
            ->get();
            
            // Categories distribution
            $categoriesDistribution = \App\Models\Product::where('company_id', $companyId)
                ->selectRaw('category_id, COUNT(*) as product_count, SUM(current_stock * price) as total_value')
                ->with('category:id,name')
                ->groupBy('category_id')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'movement_trends' => $movementTrends,
                    'top_products' => $topProducts,
                    'categories_distribution' => $categoriesDistribution,
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                ]
            ]);
        });
    });

    // Dashboard API
    Route::prefix('dashboard')->group(function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();
            $companyId = $user->company_id;
            
            $stats = [
                'products' => \App\Models\Product::where('company_id', $companyId)->count(),
                'categories' => \App\Models\Category::where('company_id', $companyId)->count(),
                'suppliers' => \App\Models\Supplier::where('company_id', $companyId)->count(),
                'low_stock' => \App\Models\Product::where('company_id', $companyId)
                                                 ->whereRaw('current_stock <= min_stock')
                                                 ->where('current_stock', '>', 0)
                                                 ->count(),
                'out_of_stock' => \App\Models\Product::where('company_id', $companyId)
                                                     ->where('current_stock', '<=', 0)
                                                     ->count(),
                'total_stock_value' => \App\Models\Product::where('company_id', $companyId)
                                                         ->sum(\DB::raw('current_stock * price')),
                'recent_movements' => \App\Models\StockMovement::whereHas('product', function ($q) use ($companyId) {
                                                                   $q->where('company_id', $companyId);
                                                               })
                                                               ->with(['product', 'user'])
                                                               ->orderBy('created_at', 'desc')
                                                               ->limit(5)
                                                               ->get(),
                'today_movements' => \App\Models\StockMovement::whereHas('product', function ($q) use ($companyId) {
                                                                   $q->where('company_id', $companyId);
                                                               })
                                                               ->whereDate('created_at', today())
                                                               ->count(),
                'alerts' => [
                    'low_stock_products' => \App\Models\Product::where('company_id', $companyId)
                                                              ->whereRaw('current_stock <= min_stock')
                                                              ->where('current_stock', '>', 0)
                                                              ->with('category')
                                                              ->limit(10)
                                                              ->get(),
                    'out_of_stock_products' => \App\Models\Product::where('company_id', $companyId)
                                                                  ->where('current_stock', '<=', 0)
                                                                  ->with('category')
                                                                  ->limit(10)
                                                                  ->get()
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        });

        Route::get('/stats', function (Request $request) {
            $user = $request->user();
            $companyId = $user->company_id;
            
            return response()->json([
                'total_products' => \App\Models\Product::where('company_id', $companyId)->count(),
                'total_categories' => \App\Models\Category::where('company_id', $companyId)->count(),
                'total_suppliers' => \App\Models\Supplier::where('company_id', $companyId)->count(),
                'low_stock_products' => \App\Models\Product::where('company_id', $companyId)
                    ->whereRaw('current_stock <= min_stock')->count(),
                'out_of_stock_products' => \App\Models\Product::where('company_id', $companyId)
                    ->where('current_stock', '<=', 0)->count(),
                'recent_movements' => \App\Models\StockMovement::whereHas('product', function ($q) use ($companyId) {
                        $q->where('company_id', $companyId);
                    })
                    ->whereDate('created_at', today())->count()
            ]);
        });

        Route::get('/low-stock', function (Request $request) {
            $user = $request->user();
            $companyId = $user->company_id;
            
            return \App\Models\Product::where('company_id', $companyId)
                ->whereRaw('current_stock <= min_stock')
                ->with(['category', 'supplier'])
                ->get();
        });

        Route::get('/recent-movements', function (Request $request) {
            $user = $request->user();
            $companyId = $user->company_id;
            
            return \App\Models\StockMovement::whereHas('product', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->with(['product', 'user'])
                ->latest()
                ->take(10)
                ->get();
        });
    });
});

// Public API (sem autenticação) - para integração com sistemas externos
Route::prefix('public')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'version' => '1.0.0'
        ]);
    });
});