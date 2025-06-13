<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// General Routes
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__.'/auth.php';

//rotas de entidades acadêmicas
require __DIR__.'/academic.php';

