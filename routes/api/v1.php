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
        Route::get('users', [UserController::class, 'index'])->name('user.users');

        Route::prefix('chats')->group(function () {
            Route::post('create', [ChatController::class, 'create'])->name('chat.create');
            Route::get('chats', [ChatController::class, 'index'])->name('chat.chats');

            Route::post('messages/send', [MessageController::class, 'send'])->name('message.send');
            Route::get('{chat_id}/messages', [MessageController::class, 'index'])->name('message.messages');
        });
    });
});
