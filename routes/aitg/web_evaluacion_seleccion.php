<?php

use App\Http\Controllers\Aitg\Evaluacion\EvaluacionController;
use App\Http\Controllers\Aitg\Seleccion\SeleccionController;
use Illuminate\Support\Facades\Route;

Route::prefix('aitg')->name('aitg.')->group(function () {
    Route::prefix('evaluacion')->name('evaluacion.')->middleware('can:VER EVALUACION AITG')->group(function () {
        Route::get('/', [EvaluacionController::class, 'index'])->name('index');
        Route::get('/convocatorias/{convocatoria}/postulaciones', [EvaluacionController::class, 'postulaciones'])->name('postulaciones');
        Route::post('/postulaciones/{postulacion}/iniciar', [EvaluacionController::class, 'iniciar'])
            ->middleware('can:EVALUAR POSTULACION AITG')
            ->name('iniciar');
        Route::get('/{evaluacion}', [EvaluacionController::class, 'show'])->name('show');
        Route::post('/{evaluacion}/guardar', [EvaluacionController::class, 'guardar'])
            ->middleware('can:EVALUAR POSTULACION AITG')
            ->name('guardar');
        Route::post('/{evaluacion}/finalizar', [EvaluacionController::class, 'finalizar'])
            ->middleware('can:EVALUAR POSTULACION AITG')
            ->name('finalizar');
    });

    Route::prefix('seleccion')->name('seleccion.')->middleware('can:VER SELECCION AITG')->group(function () {
        Route::get('/', [SeleccionController::class, 'index'])->name('index');
        Route::get('/convocatorias/{convocatoria}', [SeleccionController::class, 'candidatos'])->name('candidatos');
        Route::post('/convocatorias/{convocatoria}/confirmar', [SeleccionController::class, 'confirmar'])
            ->middleware('can:SELECCIONAR INSTRUCTOR AITG')
            ->name('confirmar');
    });
});
