<?php

use App\Http\Controllers\Aitg\PlanContratacionController;
use Illuminate\Support\Facades\Route;

Route::prefix('aitg/planes-contratacion')
    ->name('aitg.planes-contratacion.')
    ->group(function () {
        Route::get('/', [PlanContratacionController::class, 'index'])->name('index');
        Route::get('/create', [PlanContratacionController::class, 'create'])->name('create');
        Route::post('/', [PlanContratacionController::class, 'store'])->name('store');
        Route::get('/{plan}', [PlanContratacionController::class, 'show'])->name('show');
        Route::get('/{plan}/edit', [PlanContratacionController::class, 'edit'])->name('edit');
        Route::put('/{plan}', [PlanContratacionController::class, 'update'])->name('update');
        Route::delete('/{plan}', [PlanContratacionController::class, 'destroy'])->name('destroy');
    });
