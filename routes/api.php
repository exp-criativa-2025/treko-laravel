<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// General Routes
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['api'])->group(function () {
    Route::post('/register', [App\Http\Controllers\Auth\ApiAuthenticatedSessionController::class, 'register'])
    ->name('api.register');
    Route::post('/login', [App\Http\Controllers\Auth\ApiAuthenticatedSessionController::class, 'login'])
    ->name('api.login');
});

//rotas de entidades acadêmicas
require __DIR__.'/academic.php';

//rotas de doações
require __DIR__.'/donation.php';

//rotas de campanhas
require __DIR__.'/campaign.php';

