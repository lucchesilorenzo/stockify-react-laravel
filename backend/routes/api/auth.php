<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
  Route::post('/signup', [AuthController::class, 'signUp']);
  Route::post('/login', [AuthController::class, 'logIn']);
  Route::post('/logout', [AuthController::class, 'logOut'])->middleware('auth:sanctum');
});
