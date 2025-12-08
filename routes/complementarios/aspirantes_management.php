<?php

use App\Http\Controllers\Complementarios\AspiranteComplementarioController;
use Illuminate\Support\Facades\Route;

if (!defined('ROUTE_PATTERN_NUMERIC')) {
    define('ROUTE_PATTERN_NUMERIC', '[0-9]+');
}

// Rutas para gestión de aspirantes (requieren autenticación)
Route::middleware('auth')
    ->prefix('aspirantes')
    ->name('aspirantes.')
    ->group(function () {
        
        // Vista principal de gestión
        Route::get('gestion', [AspiranteComplementarioController::class, 'index'])
            ->name('gestion');
        
        // Ver aspirantes de un programa específico
        Route::get('programa/{curso}', [AspiranteComplementarioController::class, 'verAspirantes'])
            ->name('ver-por-programa');
        
        // Nota: La ruta para almacenar aspirantes está definida en gestion_aspirante.php
        // como programas-complementarios.aspirantes.store para seguir convenciones RESTful
        
        // Agregar persona existente como aspirante
        Route::post('programa/{complementarioId}/agregar-existente', [AspiranteComplementarioController::class, 'store'])
            ->name('agregar-existente')
            ->where('complementarioId', ROUTE_PATTERN_NUMERIC);
        
        Route::delete('programa/{complementarioId}/aspirantes/{aspiranteId}', [AspiranteComplementarioController::class, 'destroy'])
            ->name('destroy')
            ->where(['complementarioId' => ROUTE_PATTERN_NUMERIC, 'aspiranteId' => ROUTE_PATTERN_NUMERIC]);
        
        // Exportaciones
        Route::get('programa/{complementarioId}/exportar-excel', [AspiranteComplementarioController::class, 'exportarAspirantesExcel'])
            ->name('exportar-excel')
            ->where('complementarioId', ROUTE_PATTERN_NUMERIC);
        
        Route::get('programa/{complementarioId}/descargar-cedulas', [AspiranteComplementarioController::class, 'descargarCedulas'])
            ->name('descargar-cedulas')
            ->where('complementarioId', ROUTE_PATTERN_NUMERIC);
        
        // Estadísticas
        Route::get('programa/{complementarioId}/estadisticas-exclusion', [AspiranteComplementarioController::class, 'getEstadisticasExclusion'])
            ->name('estadisticas-exclusion')
            ->where('complementarioId', ROUTE_PATTERN_NUMERIC);
        
        // Validaciones
        Route::post('programa/{complementarioId}/validar-documentos', [AspiranteComplementarioController::class, 'validarDocumentos'])
            ->name('validar-documentos')
            ->where('complementarioId', ROUTE_PATTERN_NUMERIC);
    });
