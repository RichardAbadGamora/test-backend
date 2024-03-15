<?php

use App\Http\Controllers\API\PrivateFolderController;
use App\Http\Controllers\API\SharedFolderController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::get('shared-folders', [SharedFolderController::class, 'index'])
        ->middleware('user-can:shared-folders:read-all');
    Route::get('shared-folders/{folder}', [SharedFolderController::class, 'show'])
        ->middleware('user-can:shared-folders:read-one');
    Route::post('shared-folders', [SharedFolderController::class, 'store'])
        ->middleware('user-can:shared-folders:create');
    Route::post('shared-folders/{folder}', [SharedFolderController::class, 'update'])
        ->middleware('user-can:shared-folders:update');
    Route::delete('shared-folders/{folder}', [SharedFolderController::class, 'destroy'])
        ->middleware('user-can:shared-folders:delete');
    Route::put('shared-folders/{folder}/restore', [SharedFolderController::class, 'restore'])
        ->middleware('user-can:shared-folders:restore');

    Route::get('private-folders', [PrivateFolderController::class, 'index'])
        ->middleware('user-can:private-folders:read-all');
    Route::get('private-folders/{folder}', [PrivateFolderController::class, 'show'])
        ->middleware('user-can:private-folders:read-one');
    Route::post('private-folders', [PrivateFolderController::class, 'store'])
        ->middleware('user-can:private-folders:create');
    Route::post('private-folders/{folder}', [PrivateFolderController::class, 'update'])
        ->middleware('user-can:private-folders:update');
    Route::delete('private-folders/{folder}', [PrivateFolderController::class, 'destroy'])
        ->middleware('user-can:private-folders:delete');
    Route::put('private-folders/{folder}/restore', [PrivateFolderController::class, 'restore'])
        ->middleware('user-can:private-folders:restore');
});
