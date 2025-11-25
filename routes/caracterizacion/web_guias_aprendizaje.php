<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuiaAprendizajeController;

Route::middleware('can:VER GUIA APRENDIZAJE')->group(function () {
    Route::get('guias-aprendizaje', [GuiaAprendizajeController::class, 'index'])->name('guias-aprendizaje.index');
    Route::get('guias-aprendizaje/create', [GuiaAprendizajeController::class, 'create'])->name('guias-aprendizaje.create');
    Route::post('guias-aprendizaje', [GuiaAprendizajeController::class, 'store'])->name('guias-aprendizaje.store');
    Route::get('guias-aprendizaje/{guia-aprendizaje}', [GuiaAprendizajeController::class, 'show'])->name('guias-aprendizaje.show');
    Route::get('guias-aprendizaje/{guia-aprendizaje}/edit', [GuiaAprendizajeController::class, 'edit'])->name('guias-aprendizaje.edit');
    Route::put('guias-aprendizaje/{guia-aprendizaje}', [GuiaAprendizajeController::class, 'update'])->name('guias-aprendizaje.update');
    Route::delete('guias-aprendizaje/{guia-aprendizaje}', [GuiaAprendizajeController::class, 'destroy'])->name('guias-aprendizaje.destroy');
});
