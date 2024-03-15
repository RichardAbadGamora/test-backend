<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication-related Routes
 */
Route::group([
    'middleware' => ['localized'],
    'prefix' => 'auth',
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('authenticate', [AuthController::class, 'authenticate']);
    Route::get('login/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('login/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::get('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'updatePassword']);
    Route::post('otp', [AuthController::class, 'sendOtp']);
    Route::post('magic-link', [AuthController::class, 'sendMagicLink']);
    Route::post('magic-link/validate', [AuthController::class, 'validateMagicLink']);
    Route::post('otp/resend', [AuthController::class, 'resendOtp']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('me')->group(function () {
            Route::get('/', [AuthController::class, 'me']);
            Route::put('/', [UserController::class, 'updateGeneralInfo']);
            Route::get('/activities', [UserController::class, 'activities']);
            Route::put('/email', [UserController::class, 'changeEmail']);
            Route::post('/email/otp', [UserController::class, 'changeEmailOtp']);
            Route::post('/path-background', [UserController::class, 'updatePathBackground']);
            Route::post('/page-background', [UserController::class, 'updatePageBackground']);
            Route::put('/pages-per-row', [UserController::class, 'updatePagesPerRow']);
            Route::put('/page-gaps', [UserController::class, 'updatePageGaps']);
            Route::put('/container-margins', [UserController::class, 'updateContainerMargins']);
            Route::post('/password', [UserController::class, 'changePassword']);
        });
        Route::delete('logout', [AuthController::class, 'logout']);
    });
});
