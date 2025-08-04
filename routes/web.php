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


