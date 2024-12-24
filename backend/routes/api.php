<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/api/dashboard.php';
    require __DIR__ . '/api/products.php';
    require __DIR__ . '/api/tasks.php';
    require __DIR__ . '/api/orders.php';
    require __DIR__ . '/api/customers.php';
    require __DIR__ . '/api/suppliers.php';
    require __DIR__ . '/api/categories.php';
    require __DIR__ . '/api/warehouses.php';
    require __DIR__ . '/api/analytics.php';
    require __DIR__ . '/api/settings.php';
});
