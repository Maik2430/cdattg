<?php

use App\Http\Controllers\Aitg\Convocatoria\ConvocatoriaController;
use App\Http\Controllers\Aitg\Convocatoria\ConvocatoriaPublicaController;
use Illuminate\Support\Facades\Route;

Route::prefix('aitg')->name('aitg.')->group(function () {
    Route::prefix('convocatorias')->name('convocatorias.')->group(function () {
        Route::get('/planes-por-competencia', [ConvocatoriaController::class, 'planesPorCompetencia'])
            ->name('planes-por-competencia');

        Route::prefix('publicas')->name('publicas.')->group(function () {
            Route::get('/', [ConvocatoriaPublicaController::class, 'index'])->name('index');
            Route::get('/{convocatoria}', [ConvocatoriaPublicaController::class, 'show'])->name('show');
            Route::delete('/{convocatoria}/postulacion', [ConvocatoriaPublicaController::class, 'destroyPostulacion'])->name('postulacion.destroy');
            Route::get('/{convocatoria}/postular', [ConvocatoriaPublicaController::class, 'postular'])->name('postular');
            Route::post('/{convocatoria}/perfil', [ConvocatoriaPublicaController::class, 'seleccionarPerfil'])->name('perfil');
            Route::post('/{convocatoria}/documentos', [ConvocatoriaPublicaController::class, 'storeDocumentos'])->name('documentos.store');
            Route::post('/{convocatoria}/documentos-lote', [ConvocatoriaPublicaController::class, 'storeDocumentosLote'])->name('documentos.lote');
            Route::delete('/{convocatoria}/documentos/{postulacionArchivo}', [ConvocatoriaPublicaController::class, 'destroyDocumento'])->name('documentos.destroy');
            Route::post('/{convocatoria}/reutilizar', [ConvocatoriaPublicaController::class, 'reutilizar'])->name('reutilizar');
            Route::post('/{convocatoria}/enviar', [ConvocatoriaPublicaController::class, 'enviarPostulacion'])->name('enviar');
            Route::get('/{convocatoria}/formalizacion', [ConvocatoriaPublicaController::class, 'formalizacion'])->name('formalizacion');
            Route::post('/{convocatoria}/formalizacion/enviar', [ConvocatoriaPublicaController::class, 'enviarFormalizacion'])->name('formalizacion.enviar');
        });

        Route::get('/', [ConvocatoriaController::class, 'index'])->name('index');
        Route::get('/create', [ConvocatoriaController::class, 'create'])->name('create');
        Route::post('/', [ConvocatoriaController::class, 'store'])->name('store');
        Route::get('/{convocatoria}/postulaciones', [ConvocatoriaController::class, 'postulaciones'])->name('postulaciones');
        Route::get('/{convocatoria}/edit', [ConvocatoriaController::class, 'edit'])->name('edit');
        Route::put('/{convocatoria}', [ConvocatoriaController::class, 'update'])->name('update');
        Route::delete('/{convocatoria}', [ConvocatoriaController::class, 'destroy'])->name('destroy');
        Route::get('/{convocatoria}', [ConvocatoriaController::class, 'show'])->name('show');
    });
});
