<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\WaveAppsController;

Route::group(['prefix' => 'waveapps'], function () {
    Route::post('/authenticate', [WaveAppsController::class, 'authenticate']);
    Route::get('/{integration}/profit-and-loss-report', [WaveAppsController::class, 'generateProfitAndLossReport']);
});
