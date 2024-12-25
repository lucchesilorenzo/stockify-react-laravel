<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('tasks')->group(function () {
    Route::post('/generate', [TaskController::class, 'generateTasks']);
    Route::post('/', [TaskController::class, 'createTask']);
    Route::patch('/{task}/field', [TaskController::class, 'updateTaskField']);
    Route::patch('/{task}', [TaskController::class, 'updateTask']);
    Route::delete('/{task}', [TaskController::class, 'deleteTask']);
    Route::get('/', [TaskController::class, 'getTasks']);
});
