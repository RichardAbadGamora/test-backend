<?php

use App\Http\Controllers\API\SignNowController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'localized']], function () {
    Route::group(['prefix' => 'sign-now'], function () {
        Route::post('/', [SignNowController::class, 'storeAccessKey']);
        Route::get('/{user}/{path}', [SignNowController::class, 'index']);
        Route::get('/{token}', [SignNowController::class, 'getAllDocuments']);
        Route::post('/access-token', [SignNowController::class, 'setAuthToken']);
    });
});
