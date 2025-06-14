<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (sem autenticação)
// (Não tem rotas publicas para cmapnha)

// Rotas protegidas (com autenticação)

Route::apiResource('campaigns', CampaignController::class);