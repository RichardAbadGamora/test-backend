<?php

use App\Http\Controllers\API\IframeController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication-related Routes
 */
Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::get('/iframes', [IframeController::class, 'index']);
    Route::post('/iframes', [IframeController::class, 'store']);
});
