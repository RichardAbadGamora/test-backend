<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GmailController;

Route::group(['prefix' => 'gmail'], function () {
    Route::get('/', [GmailController::class, 'index']);
    Route::get('/{page}', [GmailController::class, 'show']);
    Route::get('/{page}/url', [GmailController::class, 'generateURL']);
    Route::post('/authenticate', [GmailController::class, 'authenticate']);
    Route::delete('/{page}/disconnect-integration', [GmailController::class, 'disconnectIntegration']);
    Route::get('/{integration}/inbox', [GmailController::class, 'getInbox']);
    Route::get('/{integration}/inbox/{messageID}', [GmailController::class, 'viewInboxMessage']);
    Route::delete('/{integration}/inbox/{messageID}', [GmailController::class, 'deleteInboxMessage']);
    Route::put('/{integration}/inbox/{messageID}/archive', [GmailController::class, 'archiveInboxMessage']);
    Route::delete('/{integration}/inbox/', [GmailController::class, 'bulkDeleteInboxMessage']);
    Route::put('/{integration}/inbox/archive', [GmailController::class, 'bulkArchiveInboxMessage']);
    Route::put('/{integration}/inbox/delete', [GmailController::class, 'bulkDeleteInboxMessage']);
    Route::post('/{integration}/inbox/{messageID}/reply', [GmailController::class, 'replyToMessage']);
});
