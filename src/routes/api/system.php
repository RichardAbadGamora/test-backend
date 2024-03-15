<?php

use App\Http\Controllers\API\SystemController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'localized']], function () {
    Route::get('/system/configs', [SystemController::class, 'getConfigs']);
});
