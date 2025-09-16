<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Landing Page Routes (public)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/features', [LandingController::class, 'features'])->name('features');
Route::get('/about', [LandingController::class, 'about'])->name('about');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');
Route::get('/demo', [LandingController::class, 'demo'])->name('demo');

// Registration Routes (public)
Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegistrationController::class, 'register'])->name('register.store');
Route::get('/register/check-availability', [RegistrationController::class, 'checkAvailability'])->name('registration.check-availability');

// Payment Routes (public)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/checkout', [App\Http\Controllers\PaymentController::class, 'showCheckout'])->name('checkout');
    Route::post('/stripe/checkout', [App\Http\Controllers\PaymentController::class, 'createStripeCheckout'])->name('stripe.checkout');
    Route::post('/pagseguro/create', [App\Http\Controllers\PaymentController::class, 'createPagSeguroPayment'])->name('pagseguro.create');
    Route::get('/success', [App\Http\Controllers\PaymentController::class, 'success'])->name('success');
    Route::get('/cancel', [App\Http\Controllers\PaymentController::class, 'cancel'])->name('cancel');
    Route::post('/stripe/webhook', [App\Http\Controllers\PaymentController::class, 'stripeWebhook'])->name('stripe.webhook');
    Route::post('/pagseguro/webhook', [App\Http\Controllers\PaymentController::class, 'pagSeguroWebhook'])->name('pagseguro.webhook');
});

// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    
    // Companies management
    Route::resource('companies', \App\Http\Controllers\SuperAdmin\CompanyController::class);
    Route::patch('companies/{company}/toggle-status', [\App\Http\Controllers\SuperAdmin\CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
    Route::get('companies/export', [\App\Http\Controllers\SuperAdmin\CompanyController::class, 'export'])->name('companies.export');
    
    // Users management
    Route::resource('users', \App\Http\Controllers\SuperAdmin\UserController::class);
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\SuperAdmin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('users/export', [\App\Http\Controllers\SuperAdmin\UserController::class, 'export'])->name('users.export');
    Route::post('users/bulk-action', [\App\Http\Controllers\SuperAdmin\UserController::class, 'bulkAction'])->name('users.bulk-action');
    
    // Plans management
    Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class);
    Route::patch('plans/{plan}/toggle-status', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'toggleStatus'])->name('plans.toggle-status');
    Route::post('plans/{plan}/duplicate', [\App\Http\Controllers\SuperAdmin\PlanController::class, 'duplicate'])->name('plans.duplicate');
    
    // Subscriptions management
    Route::resource('subscriptions', \App\Http\Controllers\SuperAdmin\SubscriptionController::class);
    Route::post('subscriptions/{subscription}/renew', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'renew'])->name('subscriptions.renew');
    Route::patch('subscriptions/{subscription}/cancel', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::patch('subscriptions/{subscription}/reactivate', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');
    Route::patch('subscriptions/{subscription}/change-plan', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');
    Route::patch('subscriptions/{subscription}/mark-as-paid', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'markAsPaid'])->name('subscriptions.mark-as-paid');
    
    // Reports and Analytics
    Route::get('/reports', [\App\Http\Controllers\SuperAdmin\ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\SuperAdmin\ReportController::class, 'export'])->name('reports.export');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/general', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/email', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateEmail'])->name('settings.email');
    Route::post('/settings/test-email', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'testEmail'])->name('settings.test-email');
    Route::post('/settings/security', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::post('/settings/maintenance', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'toggleMaintenance'])->name('settings.maintenance');
    Route::post('/settings/backup', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'createBackup'])->name('settings.backup');
    Route::get('/settings/backup-history', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'getBackupHistory'])->name('settings.backup-history');
    Route::post('/settings/clear-cache', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'clearCache'])->name('settings.clear-cache');
    Route::get('/settings/system-info', [\App\Http\Controllers\SuperAdmin\SettingsController::class, 'getSystemInfo'])->name('settings.system-info');
    
    Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
    Route::post('/users/{user}/impersonate', [SuperAdminController::class, 'impersonate'])->name('impersonate');
    Route::post('/stop-impersonating', [SuperAdminController::class, 'stopImpersonating'])->name('stop-impersonating');
});

// Main Application Routes (protected by multi-tenant middleware)
Route::middleware(['auth', 'verified', 'ensure.user.belongs.to.company'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class)->middleware('permission:view categories');
    
    // Suppliers
    Route::resource('suppliers', SupplierController::class)->middleware('permission:view suppliers');
    
    // Products
    Route::resource('products', ProductController::class)->middleware('permission:view products');
    Route::get('products/{product}/stock', [ProductController::class, 'getStock'])->name('products.stock');
    
    // Stock Movements
    Route::resource('stock-movements', StockMovementController::class)->middleware('permission:view stock movements');
    Route::get('stock-movements/product/{product}/stock', [StockMovementController::class, 'getProductStock'])->name('stock-movements.product-stock');
    
    // Users (Admin only)
    Route::resource('users', UserController::class)->middleware('permission:view users');
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Reports
    Route::prefix('reports')->name('reports.')->middleware('permission:view reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/movements', [ReportController::class, 'movements'])->name('movements');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Special routes
Route::get('/subscription/expired', function () {
    return view('subscription.expired');
})->name('subscription.expired');

require __DIR__.'/auth.php';
