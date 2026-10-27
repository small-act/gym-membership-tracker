<?php

use App\Http\Controllers\MemberPortal\AuthController;
use App\Http\Controllers\MemberPortal\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('member')->name('member.')->group(function () {
    Route::middleware('guest:member')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->middleware('throttle:6,1')
            ->name('login.store');
    });

    Route::middleware('member.auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');
    });
});