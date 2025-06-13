<?php

use App\Http\Controllers\AcademicEntityController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (sem autenticação)
Route::get('public/academic-entities', [AcademicEntityController::class, 'publicIndex'])
    ->name('public.academic-entities.index');
    
Route::get('public/academic-entities/{academic_entity}', [AcademicEntityController::class, 'publicShow'])
    ->name('public.academic-entities.show');

// Rotas protegidas (com autenticação)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('academic-entities', AcademicEntityController::class)
        ->parameters(['academic-entities' => 'academic_entity'])
        ->names([
            'index' => 'academic-entities.index',
            'store' => 'academic-entities.store',
            'show' => 'academic-entities.show',
            'update' => 'academic-entities.update',
            'destroy' => 'academic-entities.destroy',
        ]);
    
    Route::post('academic-entities/{id}/activate', [AcademicEntityController::class, 'activate'])
        ->name('academic-entities.activate');
});