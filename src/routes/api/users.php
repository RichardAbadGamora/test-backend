<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication-related Routes
 */
Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
});
