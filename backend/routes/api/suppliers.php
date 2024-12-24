<?php

use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('suppliers')->group(function () {
  Route::patch('/{supplier}/rating', [SupplierController::class, 'updateSupplierRating']);
  Route::post('/', [SupplierController::class, 'createSupplier']);
  Route::get('/', [SupplierController::class, 'getSuppliers']);
});
