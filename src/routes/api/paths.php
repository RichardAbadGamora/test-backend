<?php

use App\Http\Controllers\API\PathController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'localized']], function () {
    Route::resource('paths', PathController::class)
        ->middleware(['manages-path'])
        ->except(['store', 'index', 'update']);
    Route::post('paths/{path}', [PathController::class, 'update'])
        ->middleware(['manages-path']);
    Route::resource('paths', PathController::class)->only(['store', 'index']);

    Route::group(['prefix' => 'paths'], function () {
        Route::put('/reorder-pin', [PathController::class, 'reorderPin']);
        Route::put('{path}/pin', [PathController::class, 'pin']);
        Route::get('{path}/me', [PathController::class, 'me']);
        Route::get('{path}/activities', [PathController::class, 'activities']);
        Route::post('{path}/invite/', [PathController::class, 'inviteUser'])
            ->middleware(['manages-path', 'user-can:roles:authorized-users:invite']);
        Route::post('{path}/remove-access', [PathController::class, 'removeAccess'])
            ->middleware(['manages-path', 'user-can:roles:authorized-users:remove']);

        // path settings routes
        Route::post('{path}/path-background', [PathController::class, 'updatePathBackground'])
            ->middleware(['manages-path', 'user-can:path-settings:update-path-background']);
        Route::put('{path}/page-background', [PathController::class, 'updatePageBackground'])
            ->middleware(['manages-path', 'user-can:path-settings:update-page-background']);
        Route::put('{path}/general-info', [PathController::class, 'updateGeneralInfo'])
            ->middleware(['manages-path', 'user-can:path-settings:update-general-info']);
        Route::put('{path}/archive', [PathController::class, 'archive'])
            ->middleware(['manages-path', 'user-can:paths:archive']);
        Route::put('{path}/unarchive', [PathController::class, 'unarchive'])
            ->middleware(['manages-path', 'user-can:paths:unarchive']);
        Route::get('{path}/users', [PathController::class, 'getUsers']);
    });
});
