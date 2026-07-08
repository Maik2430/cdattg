<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Configuration\UploadLimits;
use App\Exceptions\ImportFileTempPathException;
use App\Services\PersonaImportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaImportUploadActions
{
    public function updatedArchivo(): void
    {
        Log::info('updatedArchivo llamado', [
            'archivo' => $this->archivo ? 'presente' : 'ausente',
        ]);

        if ($this->archivo) {
            $this->archivoNombre = $this->archivo->getClientOriginalName();
            Log::info('Archivo actualizado', [
                'nombre' => $this->archivoNombre,
            ]);
        } else {
            $this->archivoNombre = self::ARCHIVO_POR_DEFECTO;
        }
    }

    public function iniciarImportacion(): void
    {
        Log::info('iniciarImportacion llamado', [
            'archivo' => $this->archivo ? 'presente' : 'ausente',
            'archivoNombre' => $this->archivoNombre,
            'archivo_tipo' => $this->archivo ? get_class($this->archivo) : 'null',
        ]);

        if (! $this->archivo) {
            Log::warning('No hay archivo seleccionado', [
                'archivoNombre' => $this->archivoNombre,
            ]);
            $this->dispatch('error-importacion', [
                'message' => 'Debes seleccionar un archivo para importar.',
            ]);

            return;
        }

        if (! $this->archivo->getRealPath()) {
            Log::warning('Archivo sin ruta temporal', [
                'archivoNombre' => $this->archivoNombre,
            ]);
            $this->dispatch('error-importacion', [
                'message' => 'El archivo aún se está cargando. Por favor, espera un momento e intenta de nuevo.',
            ]);

            return;
        }

        $this->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:'.(UploadLimits::IMPORT_CONTENT_LENGTH_BYTES / 1024),
        ], [
            'archivo.required' => 'Debes seleccionar un archivo para importar.',
            'archivo.mimes' => 'El archivo debe ser de tipo XLSX o XLS.',
            'archivo.max' => 'El archivo no puede superar los '.
                (UploadLimits::IMPORT_CONTENT_LENGTH_BYTES / 1024 / 1024).'MB.',
        ]);

        try {
            if (! $this->archivo->getRealPath()) {
                throw new ImportFileTempPathException;
            }

            $tempPath = $this->archivo->getRealPath();

            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $this->archivo->getClientOriginalName(),
                $this->archivo->getMimeType(),
                null,
                true
            );

            $importService = app(PersonaImportService::class);
            $import = $importService->iniciarImportacion(
                $uploadedFile,
                Auth::id()
            );

            $this->importacionId = $import->id;
            $this->importacionSeleccionada = $import->id;
            $this->mostrarProgreso = true;
            $this->resetearProgreso();
            $this->archivo = null;
            $this->archivoNombre = self::ARCHIVO_POR_DEFECTO;

            session()->forget('import_status_'.$import->id);

            $this->dispatch('importacion-iniciada', [
                'message' => 'Importación iniciada correctamente.',
            ]);

            $this->actualizarProgreso();
        } catch (\Throwable $e) {
            $this->dispatch('error-importacion', [
                'message' => 'Error al iniciar la importación: '.$e->getMessage(),
            ]);
        }
    }
}
