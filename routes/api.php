<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Middleware\ValidateCustomerDeleteMiddleware;
use App\Http\Middleware\ValidateCustomerQueryMiddleware;
use App\Http\Middleware\ValidateCustomerStoreMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('token')->group(function () {
    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware(ValidateCustomerStoreMiddleware::class);

    Route::get('/customers', [CustomerController::class, 'show'])
        ->middleware(ValidateCustomerQueryMiddleware::class);

    Route::delete('/customers/{dni}', [CustomerController::class, 'destroy'])
        ->middleware(ValidateCustomerDeleteMiddleware::class);
});
