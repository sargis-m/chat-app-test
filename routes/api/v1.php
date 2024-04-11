<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\ChatController;
use App\Http\Controllers\API\V1\MessageController;
use App\Http\Middleware\RateLimitMiddleware;

Route::prefix('v1')->middleware(RateLimitMiddleware::class)->group(function () {
    Route::post('register', [UserController::class, 'register'])->name('user.register');
    Route::post('login', [UserController::class, 'login'])->name('user.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('users', [UserController::class, 'getUsers'])->name('user.users');

        Route::prefix('chats')->group(function () {
            Route::post('create', [ChatController::class, 'create']);
            Route::get('get_chats', [ChatController::class, 'getChatsByUser']);

            Route::post('send_message', [MessageController::class, 'sendMessage']);
            Route::get('get_messages/{chat_id}', [MessageController::class, 'getMessagesByChat']);
        });
    });
});
