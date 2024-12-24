<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signUp']);
    Route::post('/login', [AuthController::class, 'logIn']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Auth - Logout
    Route::post('/logout', [AuthController::class, 'logOut']);

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/inventory-value', [DashboardController::class, 'getInventoryValue']);
        Route::get('/low-stock-products', [DashboardController::class, 'getLowStockProducts']);
        Route::get('/shipped-orders', [DashboardController::class, 'getShippedOrders']);
        Route::get('/units-in-stock', [DashboardController::class, 'getUnitsInStock']);
        Route::get('/activities', [DashboardController::class, 'getActivities']);
    });

    // Products
    Route::prefix('products')->group(function () {
        Route::get('/to-restock', [ProductController::class, 'getProductsToRestock']);
        Route::get('/available', [ProductController::class, 'getAvailableProducts']);
        Route::get('/slug/{productSlug}', [ProductController::class, 'getProductBySlug']);
        Route::post('/{product}', [ProductController::class, 'updateProduct']);
        Route::patch('/{product}/status', [ProductController::class, 'updateProductStatus']);
        Route::get('/', [ProductController::class, 'getProducts']);
    });

    // Tasks
    Route::prefix('tasks')->group(function () {
        Route::post('/generate', [TaskController::class, 'generateTasks']);
        Route::post('/', [TaskController::class, 'createTask']);
        Route::patch('/{task}/field', [TaskController::class, 'updateTaskField']);
        Route::patch('/{task}', [TaskController::class, 'updateTask']);
        Route::delete('/{task}', [TaskController::class, 'deleteTask']);
        Route::get('/', [TaskController::class, 'getTasks']);
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/monthly', [OrderController::class, 'getMonthlyOrders']);
        Route::get('/weekly', [OrderController::class, 'getWeeklyOrders']);
        Route::get('/', [OrderController::class, 'getOrders']);
        Route::post('/restock', [OrderController::class, 'createRestockOrder']);
        Route::post('/', [OrderController::class, 'createOrder']);
        Route::patch('/{order}/status', [OrderController::class, 'updateOrderStatus']);
    });

    // Customers
    Route::prefix('customers')->group(function () {
        Route::post('/shipment', [CustomerController::class, 'createCustomerShipment']);
        Route::post('/', [CustomerController::class, 'createCustomers']);
        Route::get('/', [CustomerController::class, 'getCustomers']);
        Route::patch('/{customer}', [CustomerController::class, 'updateCustomer']);
    });

    // Suppliers
    Route::prefix('suppliers')->group(function () {
        Route::patch('/{supplier}/rating', [SupplierController::class, 'updateSupplierRating']);
        Route::post('/', [SupplierController::class, 'createSupplier']);
        Route::get('/', [SupplierController::class, 'getSuppliers']);
    });

    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'getCategories']);
    });

    // Warehouses
    Route::prefix('warehouses')->group(function () {
        Route::get('/', [WarehouseController::class, 'getWarehouses']);
    });

    // Analytics
    Route::prefix('analytics')->group(function () {
        Route::get('/products-by-category', [AnalyticsController::class, 'getProductsByCategory']);
        Route::get('/monthly-inventory-values', [AnalyticsController::class, 'getMonthlyInventoryValue']);
        Route::get('/top-products', [AnalyticsController::class, 'getTopProducts']);
    });

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'getSettingsByUserId']);
        Route::patch('/', [SettingsController::class, 'updateSettingsByUserId']);
    });
});
