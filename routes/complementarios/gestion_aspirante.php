<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\AspiranteComplementarioController;

Route::middleware('auth')->group(function () {
    // Ruta principal de gestión (index)
    Route::get('/gestion-aspirantes', [AspiranteComplementarioController::class, 'index'])
        ->name('gestion-aspirantes');

    // Esta ruta debe estar antes de la ruta genérica {programa} en web.php
    // Usar where para restringir el parámetro a strings que no sean numéricos puros
    Route::get('/programas-complementarios/{curso}', [AspiranteComplementarioController::class, 'verAspirantes'])
        ->name('programas-complementarios.ver-aspirantes')
        ->where('curso', '[^0-9]+.*'); // Acepta cualquier string que no sea solo números

    // Resource routes para aspirantes anidados en programas
    Route::prefix('programas-complementarios/{programa}')->name('programas-complementarios.')->group(function () {
        // Crear formulario de aspirante
        Route::get('/aspirantes/create', [AspiranteComplementarioController::class, 'create'])
            ->name('aspirantes.create');

        // Almacenar nuevo aspirante (RESTful)
        Route::post('/aspirantes', [AspiranteComplementarioController::class, 'store'])
            ->name('aspirantes.store');

        // Actualizar aspirante (RESTful)
        Route::put('/aspirantes/{aspirante}', [AspiranteComplementarioController::class, 'update'])
            ->name('aspirantes.update');

        // Eliminar/rechazar aspirante (RESTful)
        Route::delete('/aspirantes/{aspirante}', [AspiranteComplementarioController::class, 'destroy'])
            ->name('aspirantes.destroy');
    });

    // Rutas para acciones personalizadas
    Route::get(
        '/programas-complementarios/{complementarioId}/exportar-excel',
        [AspiranteComplementarioController::class, 'exportarAspirantesExcel']
    )->name('programas-complementarios.exportar-excel');

    Route::get(
        '/programas-complementarios/{complementarioId}/descargar-cedulas',
        [AspiranteComplementarioController::class, 'descargarCedulas']
    )->name('programas-complementarios.descargar-cedulas');

    Route::post(
        '/programas-complementarios/{complementarioId}/validar-documentos',
        [AspiranteComplementarioController::class, 'validarDocumentos']
    )->name('programas-complementarios.validar-documentos');
});

