<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('campaigns', CampaignController::class);
});
