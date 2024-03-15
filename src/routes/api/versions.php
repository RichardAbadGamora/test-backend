<?php

use App\Http\Controllers\API\VersionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::post('versions/revert', [VersionController::class, 'revert']);
});