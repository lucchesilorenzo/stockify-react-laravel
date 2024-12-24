<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('orders')->group(function () {
  Route::get('/monthly', [OrderController::class, 'getMonthlyOrders']);
  Route::get('/weekly', [OrderController::class, 'getWeeklyOrders']);
  Route::get('/', [OrderController::class, 'getOrders']);
  Route::post('/restock', [OrderController::class, 'createRestockOrder']);
  Route::post('/', [OrderController::class, 'createOrder']);
  Route::patch('/{order}/status', [OrderController::class, 'updateOrderStatus']);
});
