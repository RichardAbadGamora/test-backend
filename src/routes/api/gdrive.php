<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GdriveController;

Route::group(['middleware' => ['auth:sanctum', 'localized']], function () {
    Route::group(['prefix' => 'gdrive'], function () {
        Route::get('/{integration}', [GdriveController::class, 'index']);
        Route::get('/{page}/url', [GdriveController::class, 'generateURL']);
        Route::post('/authenticate', [GdriveController::class, 'authenticate']);
        Route::get('/list/{integration}', [GdriveController::class, 'listFiles']);
        Route::get('/list-shared-files/{integration}', [GdriveController::class, 'getAllSharedFiles']);
        Route::get('/list-all-starred-files/{integration}', [GdriveController::class, 'getAllStarredFiles']);
        Route::get('/open/{fileId}/{integration}', [GdriveController::class, 'openFile']);
        Route::post('/upload/{integration}', [GdriveController::class, 'uploadFile']);
        Route::put('/delete/{fileId}/{integration}', [GdriveController::class, 'deleteFile']);
        Route::post('/create-folder/{integration}', [GdriveController::class, 'createFolder']);
        Route::get('/details/{fileId}/{integration}', [GdriveController::class, 'getFileDetails']);
        Route::post('/rename/{integration}', [GdriveController::class, 'renameFile']);
        Route::post('/move/{integration}', [GdriveController::class, 'moveFileToFolder']);
        Route::post('/share/{integration}', [GdriveController::class, 'shareFile']);
        Route::post('/star/{integration}', [GdriveController::class, 'addToStarred']);
        Route::post('/unstar/{integration}', [GdriveController::class, 'removeFromStarred']);
    });
});
