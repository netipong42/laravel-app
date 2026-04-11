<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::middleware('api')
    ->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/me', [UserController::class, 'me']);
    });


Route::prefix('customers')
    ->middleware('api')
    ->group(function () {
        Route::get('/', [CustomerController::class, 'findAll']);
        Route::get('/{customer}', [CustomerController::class, 'findById'])->whereUuid('customer');
        Route::post('/', [CustomerController::class, 'create']);
        Route::put('/{customer}', [CustomerController::class, 'update'])->whereUuid('customer');
        Route::delete('/{customer}', [CustomerController::class, 'delete'])->whereUuid('customer');
    });


Route::post('/auth/check-token', [AuthController::class, 'checkAuthJwt']);
