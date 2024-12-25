<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {
    Route::get('/to-restock', [ProductController::class, 'getProductsToRestock']);
    Route::get('/available', [ProductController::class, 'getAvailableProducts']);
    Route::get('/slug/{productSlug}', [ProductController::class, 'getProductBySlug']);
    Route::post('/{product}', [ProductController::class, 'updateProduct']);
    Route::patch('/{product}/status', [ProductController::class, 'updateProductStatus']);
    Route::get('/', [ProductController::class, 'getProducts']);
});
