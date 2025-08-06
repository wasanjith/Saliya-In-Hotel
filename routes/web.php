<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;

Route::get('/', function () {
    return redirect('/pos');
});

// POS Routes
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::get('/pos/category/{categoryId}/items', [POSController::class, 'getFoodItemsByCategory'])->name('pos.category.items');
Route::post('/pos/order', [POSController::class, 'storeOrder'])->name('pos.order.store');
Route::get('/orders/{order}', [POSController::class, 'getOrderDetails'])->name('orders.show');

// Table Management Routes
Route::get('/tables', [App\Http\Controllers\TableController::class, 'index'])->name('tables.index');
Route::get('/api/tables', [App\Http\Controllers\TableController::class, 'getTables'])->name('api.tables');
Route::post('/api/assign-table', [App\Http\Controllers\TableController::class, 'assignTable'])->name('api.assign-table');
Route::post('/api/clear-table', [App\Http\Controllers\TableController::class, 'clearTable'])->name('api.clear-table');
Route::post('/api/close-order', [App\Http\Controllers\TableController::class, 'closeOrder'])->name('api.close-order');




