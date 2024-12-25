<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::prefix('customers')->group(function () {
    Route::post('/shipment', [CustomerController::class, 'createCustomerShipment']);
    Route::post('/', [CustomerController::class, 'createCustomers']);
    Route::get('/', [CustomerController::class, 'getCustomers']);
    Route::patch('/{customer}', [CustomerController::class, 'updateCustomer']);
});
