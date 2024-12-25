<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::prefix('analytics')->group(function () {
    Route::get('/products-by-category', [AnalyticsController::class, 'getProductsByCategory']);
    Route::get('/monthly-inventory-values', [AnalyticsController::class, 'getMonthlyInventoryValue']);
    Route::get('/top-products', [AnalyticsController::class, 'getTopProducts']);
});
