<?php

use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (sem autenticação)
// (Não tem rotas publicas para donations)

// Rotas protegidas (com autenticação)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('donations', DonationController::class)
        ->parameters(['donations' => 'donation'])
        ->names([
            'index' => 'donations.index',
            'store' => 'donations.store',
            'show' => 'donations.show',
            'update' => 'donations.update',
            'destroy' => 'donations.destroy',
        ]);
});