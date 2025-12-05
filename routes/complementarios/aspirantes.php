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

    // Mostrar formulario para crear nuevo aspirante
    Route::get('/aspirantes/programa/{programa}/create', [AspiranteComplementarioController::class, 'create'])
        ->name('aspirantes.create');

    // Almacenar nuevo aspirante (agregar persona existente)
    Route::post('/aspirantes/programa/{programa}/store', [AspiranteComplementarioController::class, 'store'])
        ->name('aspirantes.store');

    // Nota: La ruta 'aspirantes.agregar-existente' está definida en aspirantes_management.php
    // para mantener compatibilidad con vistas existentes

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

