<?php

use App\Http\Controllers\API\PrivateTaskController;
use App\Http\Controllers\API\SharedTaskController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::get('shared-tasks', [SharedTaskController::class, 'index'])
        ->middleware('user-can:shared-tasks:read-all');

    Route::put('shared-tasks/reposition', [SharedTaskController::class, 'reposition'])
        ->middleware('user-can:shared-tasks:update');

    Route::get('shared-tasks/{task}', [SharedTaskController::class, 'show'])
        ->middleware('user-can:shared-tasks:read-one');

    Route::post('shared-tasks', [SharedTaskController::class, 'store'])
        ->middleware('user-can:shared-tasks:create');

    Route::post('shared-tasks/{task}', [SharedTaskController::class, 'update'])
        ->middleware('user-can:shared-tasks:update');

    Route::delete('shared-tasks/{task}', [SharedTaskController::class, 'destroy'])
        ->middleware('user-can:shared-tasks:delete');

    Route::put('shared-tasks/{task}/status', [SharedTaskController::class, 'updateStatus'])
        ->middleware('user-can:shared-tasks:update');
});

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::get('private-tasks', [PrivateTaskController::class, 'index'])
        ->middleware('user-can:private-tasks:read-all');

    Route::put('private-tasks/reposition', [PrivateTaskController::class, 'reposition'])
        ->middleware('user-can:private-tasks:update');

    Route::get('private-tasks/{task}', [PrivateTaskController::class, 'show'])
        ->middleware('user-can:private-tasks:read-one');

    Route::post('private-tasks', [PrivateTaskController::class, 'store'])
        ->middleware('user-can:private-tasks:create');

    Route::post('private-tasks/{task}', [PrivateTaskController::class, 'update'])
        ->middleware('user-can:private-tasks:update');

    Route::delete('private-tasks/{task}', [PrivateTaskController::class, 'destroy'])
        ->middleware('user-can:private-tasks:delete');

    Route::put('private-tasks/{task}/status', [PrivateTaskController::class, 'updateStatus'])
        ->middleware('user-can:private-tasks:update');
});
