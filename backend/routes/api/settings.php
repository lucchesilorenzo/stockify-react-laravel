<?php

use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'getUserSettings']);
    Route::patch('/', [SettingsController::class, 'updateUserSettings']);
});
