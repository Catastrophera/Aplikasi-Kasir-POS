<?php

use Illuminate\Support\Facades\Route;
use App\Models\Menu;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShiftController;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Category;
use Illuminate\Http\Request;

// ─── Auth (public) ─────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Protected Routes ──────────────────────────────────────────────────────────
Route::middleware(\App\Http\Middleware\PosAuth::class)->group(function () {

    // Dashboard
    Route::get('/', function () {
        $todayRevenue   = Transaction::whereDate('created_at', today())->sum('total_price');
        $todayOrders    = Transaction::whereDate('created_at', today())->count();
        $todayItems     = TransactionItem::whereHas('transaction', fn($q) => $q->whereDate('created_at', today()))->sum('quantity');
        $avgTransaction = $todayOrders > 0 ? $todayRevenue / $todayOrders : 0;

        $monthRevenue = Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price');
        $monthOrders  = Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $topItems = TransactionItem::with('menu')
            ->selectRaw('menu_id, sum(quantity) as total_qty')
            ->groupBy('menu_id')->orderByDesc('total_qty')->take(5)->get();

        $recentDays = Transaction::selectRaw('DATE(created_at) as date, sum(total_price) as total, count(*) as count')
            ->groupBy('date')->orderByDesc('date')->take(7)->get();

        $recentTransactions = Transaction::whereDate('created_at', today())->latest()->take(10)->get();

        return view('dashboard', compact(
            'todayRevenue', 'todayOrders', 'todayItems', 'avgTransaction',
            'monthRevenue', 'monthOrders',
            'topItems', 'recentDays', 'recentTransactions'
        ));
    });

    // POS
    Route::get('/pos', function () {
        $menus      = Menu::with('category')->where('is_active', true)->get()->sortBy(fn($m) => $m->category?->sort_order ?? 99)->values();
        $categories = Category::orderBy('sort_order')->pluck('name');
        return view('pos', compact('menus', 'categories'));
    });

    // Transactions
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

    // Menus CRUD
    Route::get('/menus', function () {
        $menus      = Menu::with('category')->get()->sortBy(fn($m) => $m->category?->sort_order ?? 99);
        $categories = Category::orderBy('sort_order')->get();
        return view('menus.index', compact('menus', 'categories'));
    });
    Route::post('/menus', [MenuController::class, 'store']);
    Route::put('/menus/{menu}', [MenuController::class, 'update']);
    Route::delete('/menus/{menu}', [MenuController::class, 'destroy']);

    // Categories CRUD
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // History (with filters)
    Route::get('/history', function (Request $request) {
        $query = Transaction::with('items.menu')->latest();
        if ($request->filled('date'))    $query->whereDate('created_at', $request->date);
        if ($request->filled('channel')) $query->where('channel', $request->channel);
        if ($request->filled('payment')) $query->where('payment_method', $request->payment);

        $transactions   = $query->get();
        $channels       = Transaction::distinct()->orderBy('channel')->pluck('channel');
        $paymentMethods = Transaction::distinct()->orderBy('payment_method')->pluck('payment_method');

        return view('history.index', compact('transactions', 'channels', 'paymentMethods'));
    });

    // Reports
    Route::get('/reports', function () {
        $revenueByChannel = Transaction::selectRaw('channel, sum(total_price) as total, count(*) as count')
            ->groupBy('channel')->orderByDesc('total')->get();

        $revenueByPayment = Transaction::selectRaw('payment_method, sum(total_price) as total, count(*) as count')
            ->groupBy('payment_method')->orderByDesc('total')->get();

        $recentDays = Transaction::selectRaw('DATE(created_at) as date, sum(total_price) as total, count(*) as count')
            ->groupBy('date')->orderByDesc('date')->take(30)->get();

        $topMenus = TransactionItem::with('menu')
            ->selectRaw('menu_id, sum(quantity) as total_qty')
            ->groupBy('menu_id')->orderByDesc('total_qty')->take(10)->get();

        $grandTotal = Transaction::sum('total_price');
        $grandCount = Transaction::count();
        $monthTotal = Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_price');
        $monthCount = Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $bestDay = Transaction::selectRaw('DATE(created_at) as date, sum(total_price) as total')
            ->groupBy('date')->orderByDesc('total')->first();

        $activeDays = Transaction::whereMonth('created_at', now()->month)
            ->selectRaw('COUNT(DISTINCT DATE(created_at)) as days')->value('days') ?: 1;
        $avgPerDay = $monthTotal / $activeDays;

        return view('reports.index', compact(
            'revenueByChannel', 'revenueByPayment', 'recentDays', 'topMenus',
            'grandTotal', 'grandCount', 'monthTotal', 'monthCount',
            'bestDay', 'activeDays', 'avgPerDay'
        ));
    });

    // ─── Shift ────────────────────────────────────────────────────────────────
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
    Route::get('/shift/{shift}', [ShiftController::class, 'show'])->name('shift.show');
    Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');
    Route::post('/shift/{shift}/close', [ShiftController::class, 'close'])->name('shift.close');

    // Cash Drawer Management
    Route::post('/shift/cash-drawers', [ShiftController::class, 'storeCashDrawer'])->name('cash-drawers.store');
    Route::delete('/shift/cash-drawers/{cashDrawer}', [ShiftController::class, 'destroyCashDrawer'])->name('cash-drawers.destroy');

    // ─── Cash Flow (Arus Keuangan) ────────────────────────────────────────────
    Route::get('/reports/cash-flow', [\App\Http\Controllers\CashFlowController::class, 'index'])->name('cash-flow.index');
    Route::post('/reports/cash-flow', [\App\Http\Controllers\CashFlowController::class, 'store'])->name('cash-flow.store');
    Route::delete('/reports/cash-flow/{cashFlowEntry}', [\App\Http\Controllers\CashFlowController::class, 'destroy'])->name('cash-flow.destroy');

    // ─── Management ──────────────────────────────────────────────────────────
    Route::resource('/management/contacts', \App\Http\Controllers\ContactController::class);
    Route::resource('/management/purchases', \App\Http\Controllers\PurchaseController::class);
    
    Route::get('/management/stock', [\App\Http\Controllers\RawMaterialController::class, 'index'])->name('management.stock');
    Route::post('/management/stock/raw-materials', [\App\Http\Controllers\RawMaterialController::class, 'store'])->name('raw-materials.store');
    Route::put('/management/stock/raw-materials/{rawMaterial}', [\App\Http\Controllers\RawMaterialController::class, 'update'])->name('raw-materials.update');
    Route::delete('/management/stock/raw-materials/{rawMaterial}', [\App\Http\Controllers\RawMaterialController::class, 'destroy'])->name('raw-materials.destroy');
    
    Route::post('/management/stock/recipes', [\App\Http\Controllers\RawMaterialController::class, 'storeRecipe'])->name('recipes.store');
    Route::delete('/management/stock/recipes/{recipe}', [\App\Http\Controllers\RawMaterialController::class, 'destroyRecipe'])->name('recipes.destroy');
    
    Route::get('/management/stock/low-alerts', [\App\Http\Controllers\RawMaterialController::class, 'lowStockAlerts'])->name('stock.low-alerts');

    // ─── Laporan Tambahan ─────────────────────────────────────────────────────
    Route::get('/reports/categories', function() {
        $categorySales = \Illuminate\Support\Facades\DB::table('transaction_items')
            ->join('menus', 'transaction_items.menu_id', '=', 'menus.id')
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', 
                     \Illuminate\Support\Facades\DB::raw('SUM(transaction_items.quantity) as total_qty'),
                     \Illuminate\Support\Facades\DB::raw('SUM(transaction_items.price * transaction_items.quantity) as total_amount'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_amount')
            ->get();
        return view('reports.categories', compact('categorySales'));
    })->name('reports.categories');

    // ─── Settings ─────────────────────────────────────────────────────────────
    Route::get('/settings', fn() => view('settings.index'))->name('settings.index');
});
