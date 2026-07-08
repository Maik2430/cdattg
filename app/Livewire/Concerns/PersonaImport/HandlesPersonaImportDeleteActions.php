<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Models\PersonaImport;

trait HandlesPersonaImportDeleteActions
{
    public function eliminarImportacion($importId): void
    {
        try {
            $importacion = PersonaImport::findOrFail($importId);

            $this->eliminarImportacionEnTransaccion($importacion);
            $this->eliminarArchivoImportacionSiExiste($importacion);

            if ($this->importacionId === $importId) {
                $this->importacionId = null;
                $this->importacionSeleccionada = null;
                $this->mostrarProgreso = false;
                $this->resetearProgreso();
            }

            $this->cargarImportaciones();

            $this->dispatch('importacion-eliminada', [
                'message' => 'La importación fue eliminada correctamente.',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('error-eliminar', [
                'message' => 'Error al eliminar la importación: '.$e->getMessage(),
            ]);
        }
    }
}
