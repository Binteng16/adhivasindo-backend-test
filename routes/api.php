<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExternalSearchController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);

    Route::prefix('search')->group(function () {
        Route::get('/nama', [ExternalSearchController::class, 'searchNama']);
        Route::get('/nim', [ExternalSearchController::class, 'searchNim']);
        Route::get('/ymd', [ExternalSearchController::class, 'searchYmd']);
    });
});
