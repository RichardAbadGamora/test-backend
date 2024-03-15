<?php

use App\Http\Controllers\API\PathController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ChannelController;


Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'localized']], function () {
    Route::resource('channels', ChannelController::class)
        ->middleware(['manages-path']);
    Route::post('channels/sub-channel', [ChannelController::class, 'storeSubChannel'])
        ->middleware(['manages-path', 'user-can:channels:create-sub-channel']);
});
