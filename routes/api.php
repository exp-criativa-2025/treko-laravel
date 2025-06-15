<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::post('/register', [App\Http\Controllers\Auth\ApiAuthenticatedSessionController::class, 'register'])
        ->name('api.register');
    Route::post('/login', [App\Http\Controllers\Auth\ApiAuthenticatedSessionController::class, 'login'])
        ->name('api.login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
            return $request->user();
    });

    Route::post('/logout', [App\Http\Controllers\Auth\ApiAuthenticatedSessionController::class, 'logout'])
        ->name('api.logout');

    Route::post('/reset-password', [App\Http\Controllers\Auth\ApiAuthenticatedSessionController::class, 'resetPassword'])
        ->name('api.reset-password');

    Route::post('/validate-reset-code', [App\Http\Controllers\Auth\ApiAuthenticatedSessionController::class, 'validateResetCode'])
        ->name('api.validate-reset-code');

    Route::post('/change-password', [App\Http\Controllers\Auth\ApiAuthenticatedSessionController::class, 'changePassword'])
        ->name('api.change-password');
});

//rotas de entidades acadêmicas
require __DIR__ . '/academic.php';

//rotas de doações
require __DIR__ . '/donation.php';

//rotas de campanhas
require __DIR__ . '/campaign.php';
