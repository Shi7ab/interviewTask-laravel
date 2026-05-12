<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;

Route::middleware(['throttle:5,1'])->group(function () {

    Route::post('/tasks', [TaskController::class, 'create']);
});

Route::get('/tasks', [TaskController::class, 'readAll']);
Route::get('/tasks/{id}', [TaskController::class, 'read']);

Route::middleware(['role:admin','auth:sanctum'])->group(function () {

    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'delete']);

});

