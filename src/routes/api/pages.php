<?php

use App\Http\Controllers\API\PrivateTaskController;
use App\Http\Controllers\API\PageController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'pages',
    'middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized',]
], function () {
    Route::get('', [PageController::class, 'index'])
        ->middleware('user-can:pages:read-all');

    Route::get('{page}', [PageController::class, 'show'])
        ->middleware('user-can:pages:read-one');

    Route::post('', [PageController::class, 'store'])
        ->middleware('user-can:pages:create');

    Route::put('reposition', [PageController::class, 'reposition'])
        ->middleware('user-can:pages:reposition');

    Route::put('float-reposition', [PageController::class, 'floatReposition'])
        ->middleware('user-can:pages:float-reposition');

    Route::put('{page}', [PageController::class, 'update'])
        ->middleware('user-can:pages:update');

    Route::delete('{page}', [PageController::class, 'destroy'])
        ->middleware('user-can:pages:delete');
});
