<?php

use App\Http\Controllers\API\PhaseController;
use App\Http\Controllers\API\PhaseItemController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::get('phases', [PhaseController::class, 'index'])
        ->middleware('user-can:phases:read-all');
    Route::post('phases', [PhaseController::class, 'store'])
        ->middleware('user-can:phases:create');
    Route::get('phases/{phase}', [PhaseController::class, 'show'])
        ->middleware('user-can:phases:read-one');
    Route::post('phases/{phase}', [PhaseController::class, 'update'])
        ->middleware('user-can:phases:update');
    Route::delete('phases/{phase}', [PhaseController::class, 'destroy'])
        ->middleware('user-can:phases:delete');
    Route::put('phases/{phase}/restore', [PhaseController::class, 'restore'])
        ->middleware('user-can:phases:restore');

    Route::get('phase-items', [PhaseItemController::class, 'index'])
        ->middleware('user-can:phase-items:read-all');
    Route::post('phase-items', [PhaseItemController::class, 'store'])
        ->middleware('user-can:phase-items:create');
    Route::get('phase-items/{phaseItem}', [PhaseItemController::class, 'show'])
        ->middleware('user-can:phase-items:read-one');
    Route::post('phase-items/{phaseItem}', [PhaseItemController::class, 'update'])
        ->middleware('user-can:phase-items:update');
    Route::delete('phase-items/{phaseItem}', [PhaseItemController::class, 'destroy'])
        ->middleware('user-can:phase-items:delete');
    Route::put('phase-items/{phaseItem}/restore', [PhaseItemController::class, 'restore'])
        ->middleware('user-can:phase-items:restore');
});
