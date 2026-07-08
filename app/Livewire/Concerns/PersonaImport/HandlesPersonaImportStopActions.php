<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Models\PersonaImport;

trait HandlesPersonaImportStopActions
{
    public function detenerImportacion(): void
    {
        if (! $this->importacionId) {
            return;
        }

        $this->dispatch('confirmar-detener', [
            'importId' => $this->importacionId,
        ]);
    }

    public function confirmarDetener($importId): void
    {
        try {
            $importacion = PersonaImport::findOrFail($importId);

            $this->eliminarImportacionEnTransaccion($importacion);
            $this->eliminarArchivoImportacionSiExiste($importacion);

            $this->importacionId = null;
            $this->importacionSeleccionada = null;
            $this->mostrarProgreso = false;
            $this->resetearProgreso();
            $this->cargarImportaciones();

            $this->dispatch('importacion-detendida', [
                'message' => 'La importación fue detenida y eliminada correctamente.',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('error-detener', [
                'message' => 'Error al detener la importación: '.$e->getMessage(),
            ]);
        }
    }
}
