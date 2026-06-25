<?php

use App\Http\Controllers\Aitg\Banco\BancoInstructorController;
use App\Http\Controllers\Aitg\Banco\MotivoRechazoController;
use App\Http\Controllers\Aitg\Banco\TipoArchivoController;
use App\Http\Controllers\Aitg\Banco\ValidacionBancoController;
use Illuminate\Support\Facades\Route;

Route::prefix('aitg')->name('aitg.')->group(function () {
    Route::prefix('banco-instructores')->name('banco-instructores.')->group(function () {
        Route::get('/', [BancoInstructorController::class, 'index'])->name('index');
        Route::post('/documentos', [BancoInstructorController::class, 'store'])->name('documentos.store');
        Route::post('/enviar-revision', [BancoInstructorController::class, 'enviarRevision'])->name('enviar-revision');
        Route::get('/documentos/{documento}/descargar', [BancoInstructorController::class, 'download'])->name('documentos.download');
        Route::delete('/documentos/{documento}', [BancoInstructorController::class, 'destroy'])->name('documentos.destroy');
    });

    Route::prefix('validacion-banco')->name('validacion-banco.')->middleware('can:VER SOLICITUD BANCO AITG')->group(function () {
        Route::get('/', [ValidacionBancoController::class, 'index'])->name('index');
        Route::get('/{solicitud}', [ValidacionBancoController::class, 'show'])->name('show');
        Route::post('/documentos/{documento}/validar', [ValidacionBancoController::class, 'validar'])
            ->middleware('can:VALIDAR DOCUMENTO BANCO AITG')
            ->name('documentos.validar');
    });

    Route::resource('tipos-archivo', TipoArchivoController::class)
        ->parameters(['tipos-archivo' => 'tipoArchivo'])
        ->except(['show']);

    Route::resource('motivos-rechazo', MotivoRechazoController::class)
        ->parameters(['motivos-rechazo' => 'motivoRechazo'])
        ->except(['show']);
});
