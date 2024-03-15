<?php

use App\Http\Controllers\API\PrivateFileController;
use App\Http\Controllers\API\SharedFileController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::get('shared-files', [SharedFileController::class, 'index'])
        ->middleware('user-can:shared-files:read-all');
    Route::get('shared-files/{file}', [SharedFileController::class, 'show'])
        ->middleware('user-can:shared-files:read-one');
    Route::post('shared-files', [SharedFileController::class, 'store'])
        ->middleware('user-can:shared-files:create');
    Route::post('shared-files/{file}', [SharedFileController::class, 'update'])
        ->middleware('user-can:shared-files:update');
    Route::delete('shared-files/{file}', [SharedFileController::class, 'destroy'])
        ->middleware('user-can:shared-files:delete');
    Route::put('shared-files/{file}/restore', [SharedFileController::class, 'restore'])
        ->middleware('user-can:shared-files:restore');

    Route::get('private-files', [PrivateFileController::class, 'index'])
        ->middleware('user-can:private-files:read-all');
    Route::get('private-files/{file}', [PrivateFileController::class, 'show'])
        ->middleware('user-can:private-files:read-one');
    Route::post('private-files', [PrivateFileController::class, 'store'])
        ->middleware('user-can:private-files:create');
    Route::post('private-files/{file}', [PrivateFileController::class, 'update'])
        ->middleware('user-can:private-files:update');
    Route::delete('private-files/{file}', [PrivateFileController::class, 'destroy'])
        ->middleware('user-can:private-files:delete');
    Route::put('private-files/{file}/restore', [PrivateFileController::class, 'restore'])
        ->middleware('user-can:private-files:restore');
});
