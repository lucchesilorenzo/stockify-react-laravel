<?php

use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::prefix('warehouses')->group(function () {
  Route::get('/', [WarehouseController::class, 'getWarehouses']);
});
