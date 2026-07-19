<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('token')->group(function () {
    Route::get('/regions', [RegionController::class, 'index']);

    Route::get('/customers/all', [CustomerController::class, 'index']);

    Route::post('/customers', [CustomerController::class, 'store']);

    Route::get('/customers', [CustomerController::class, 'show']);

    Route::delete('/customers/{dni}', [CustomerController::class, 'destroy']);
});
