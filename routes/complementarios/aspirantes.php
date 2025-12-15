<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Complementarios\AspiranteComplementarioController;

// Rutas RESTful para aspirantes complementarios
// Nota: El middleware 'auth' ya se aplica desde web.php mediante $loadRouteFolders
Route::group([], function () {
    // Lista de programas complementarios (gestión de aspirantes)
    Route::get('/aspirantes', [AspiranteComplementarioController::class, 'index'])
        ->name('aspirantes.index');

    // Lista de aspirantes de un programa específico
    Route::get('/aspirantes/programa/{programa}', [AspiranteComplementarioController::class, 'programa'])
        ->name('aspirantes.programa');

    // Buscar persona por documento
    Route::post('/aspirantes/buscar-persona', [AspiranteComplementarioController::class, 'buscarPersona'])
        ->name('aspirantes.buscar-persona');

    // Nota: Las rutas para crear y almacenar aspirantes están definidas en gestion_aspirante.php
    // con nombres: programas-complementarios.aspirantes.create y programas-complementarios.aspirantes.store

    // Crear nueva persona y agregarla como aspirante
    Route::post('/aspirantes/programa/{programa}/create-new', [AspiranteComplementarioController::class, 'storeNewAspirante'])
        ->name('aspirantes.store-new');

    // Nota: Las siguientes rutas están definidas en aspirantes_management.php
    // para mantener compatibilidad con vistas existentes:
    // - aspirantes.destroy
    // - aspirantes.exportar-excel
    // - aspirantes.descargar-cedulas
    // - aspirantes.estadisticas-exclusion
});

