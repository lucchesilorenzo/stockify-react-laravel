<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->group(function () {
  Route::get('/inventory-value', [DashboardController::class, 'getInventoryValue']);
  Route::get('/low-stock-products', [DashboardController::class, 'getLowStockProducts']);
  Route::get('/shipped-orders', [DashboardController::class, 'getShippedOrders']);
  Route::get('/units-in-stock', [DashboardController::class, 'getUnitsInStock']);
  Route::get('/activities', [DashboardController::class, 'getActivities']);
});
