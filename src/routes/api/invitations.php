<?php

use App\Enums\InvitationType;
use App\Http\Controllers\API\InvitationController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication-related Routes
 */
Route::group([
    'prefix' => 'invitations',
    'middleware' => ['localized'],
], function () {
    Route::get('/', [InvitationController::class, 'index'])
        ->middleware(['manages-path', 'auth:sanctum']);

    Route::post('/validate', [InvitationController::class, 'verify']);
    Route::delete('/{invitation}', [InvitationController::class, 'destroy'])
        ->middleware(['manages-path', 'auth:sanctum']);
    Route::get('/{invitation}/cancel', [InvitationController::class, 'cancel'])
        ->middleware(['manages-path', 'auth:sanctum']);
    Route::post('/{invitation}/' . InvitationType::REG_AND_JOIN_PATH, [InvitationController::class, 'registerAndJoinPath'])
        ->middleware(['auth:sanctum']);
    Route::post('/{invitation}/' . InvitationType::JOIN_PATH, [InvitationController::class, 'joinPath'])
        ->middleware(['auth:sanctum']);
});
