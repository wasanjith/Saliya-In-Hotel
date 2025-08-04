<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\POSController;
use App\Http\Controllers\TablesController;

Route::get('/', function () {
    return redirect('/pos');
});

// POS Routes
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::get('/pos/category/{categoryId}/items', [POSController::class, 'getFoodItemsByCategory'])->name('pos.category.items');
Route::post('/pos/order', [POSController::class, 'storeOrder'])->name('pos.order.store');
Route::get('/orders/{order}', [POSController::class, 'getOrderDetails'])->name('orders.show');

// Tables Routes
Route::get('/tables', [TablesController::class, 'index'])->name('tables.index');
Route::patch('/tables/{table}/status', [TablesController::class, 'updateStatus'])->name('tables.update-status');
Route::post('/tables/{table}/assign-order', [TablesController::class, 'assignOrder'])->name('tables.assign-order');
Route::get('/tables/{table}/orders', [TablesController::class, 'getTableOrders'])->name('tables.orders');
Route::post('/orders/{order}/complete', [TablesController::class, 'completeOrder'])->name('orders.complete');


