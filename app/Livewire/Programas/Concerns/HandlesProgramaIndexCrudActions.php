<?php

namespace App\Livewire\Programas\Concerns;

use App\Models\ProgramaFormacion;

trait HandlesProgramaIndexCrudActions
{
    public function deletePrograma($programaId)
    {
        $programa = ProgramaFormacion::with(['competencias', 'fichasCaracterizacion'])->find($programaId);

        if (! $programa) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Programa no encontrado',
            ]);

            return;
        }

        if ($programa->competencias->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar el programa. Tiene '.$programa->competencias->count().' competencias asociadas.',
            ]);

            return;
        }

        if ($programa->fichasCaracterizacion->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar el programa. Tiene '.$programa->fichasCaracterizacion->count().' fichas asociadas.',
            ]);

            return;
        }

        try {
            $programa->delete();
            $this->closeDeleteModal();
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Programa eliminado correctamente',
            ]);
            $this->dispatch('programaEliminado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar el programa: '.$e->getMessage(),
            ]);
        }
    }

    public function toggleStatus($programaId)
    {
        $programa = ProgramaFormacion::find($programaId);

        if ($programa) {
            $programa->status = ! $programa->status;
            $programa->user_edit_id = auth()->id();
            $programa->save();

            $statusText = $programa->status ? 'activado' : 'desactivado';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Programa {$statusText} correctamente",
            ]);

            if ($this->showShowModal && $this->selectedPrograma && $this->selectedPrograma->id == $programaId) {
                $this->selectedPrograma = $programa;
            }
        }
    }
}
