<?php

use App\Http\Controllers\API\ChatMessageController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication-related Routes
 */
Route::group(['middleware' => ['auth:sanctum', 'hash-to-id', 'manages-path', 'localized']], function () {
    Route::get('/chat-messages', [ChatMessageController::class, 'index']);
    Route::post('/chat-messages', [ChatMessageController::class, 'store']);
    Route::get('/chat-messages/{page}/history', [ChatMessageController::class, 'history']);
    Route::get('/chat-messages/{session}/conversation', [ChatMessageController::class, 'conversation']);
});
