<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Middleware\RateLimitMiddleware;

Route::prefix('v1')->middleware(RateLimitMiddleware::class)->group(function () {
    Route::post('register', [UserController::class, 'register'])->name('user.register');
    Route::post('login', [UserController::class, 'login'])->name('user.login');
    Route::get('users', [UserController::class, 'getUsers'])->name('user.users');
});
