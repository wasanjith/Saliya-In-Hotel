<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrinterController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Test route to verify authentication
Route::get('/test-auth', function () {
    if (auth()->check()) {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->user()->only(['id', 'name', 'email', 'job_role'])
        ]);
    }
    return response()->json(['authenticated' => false]);
});

// Test route to verify POS access
Route::get('/test-pos-access', function () {
    if (auth()->check()) {
        return response()->json([
            'success' => true,
            'message' => 'POS access granted',
            'user' => auth()->user()->only(['id', 'name', 'email', 'job_role'])
        ]);
    }
    return response()->json([
        'success' => false,
        'message' => 'Authentication required'
    ], 401);
});

// Redirect root to login if not authenticated, otherwise to POS
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/pos');
    }
    return redirect('/login');
});

// Protected POS Routes
Route::middleware(['auth.pos'])->group(function () {
    // POS Routes
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::get('/pos/category/{categoryId}/items', [POSController::class, 'getFoodItemsByCategory'])->name('pos.category.items');
    Route::post('/pos/order', [POSController::class, 'storeOrder'])->name('pos.order.store');

    // Table Management Routes
    Route::get('/tables', [App\Http\Controllers\TableController::class, 'index'])->name('tables.index');
    Route::get('/api/tables', [App\Http\Controllers\TableController::class, 'getTables'])->name('api.tables');
    Route::post('/api/assign-table', [App\Http\Controllers\TableController::class, 'assignTable'])->name('api.assign-table');
    Route::post('/api/clear-table', [App\Http\Controllers\TableController::class, 'clearTable'])->name('api.clear-table');
    Route::post('/api/close-order', [App\Http\Controllers\TableController::class, 'closeOrder'])->name('api.close-order');

    // Customer Management Routes
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/customers/statistics', [CustomerController::class, 'statistics'])->name('customers.statistics');
    
    // Order Management Routes
    Route::resource('orders', OrderController::class);
    Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
    Route::get('/orders/statistics', [OrderController::class, 'statistics'])->name('orders.statistics');
    
    // Override the orders.show route to use POSController for JSON responses
    Route::get('/orders/{order}', [POSController::class, 'getOrderDetails'])->name('orders.show');
    
    // Printer Routes
    Route::post('/print/thermal-invoice', [PrinterController::class, 'printThermalInvoice'])->name('print.thermal-invoice');
    Route::post('/print/web-invoice', [PrinterController::class, 'printWebInvoice'])->name('print.web-invoice');
    Route::get('/print/invoice/{orderId}', [PrinterController::class, 'showInvoice'])->name('print.show-invoice');
    Route::get('/print/download-thermal/{orderId}', [PrinterController::class, 'downloadThermalInvoice'])->name('print.download-thermal');
    
    // API Routes for Customer Search
    Route::get('/api/customers', [CustomerController::class, 'apiSearch'])->name('api.customers.search');
    Route::post('/api/customers', [CustomerController::class, 'apiStore'])->name('api.customers.store');
});
