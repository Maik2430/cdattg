<?php

namespace App\Livewire\RedConocimiento\Concerns;

use App\Models\RedConocimiento;

trait HandlesRedConocimientoIndexCrudActions
{
    public function deleteRed($redId)
    {
        $red = RedConocimiento::with(['programasFormacion'])->find($redId);

        if (! $red) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Red de conocimiento no encontrada',
            ]);

            return;
        }

        if ($red->programasFormacion->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar la red. Tiene '.$red->programasFormacion->count().' programas asociados.',
            ]);

            return;
        }

        try {
            $red->delete();
            $this->closeDeleteModal();
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Red de conocimiento eliminada correctamente',
            ]);
            $this->dispatch('redEliminada');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar la red: '.$e->getMessage(),
            ]);
        }
    }

    public function toggleStatus($redId)
    {
        $red = RedConocimiento::find($redId);

        if ($red) {
            $red->status = ! $red->status;
            $red->user_edit_id = auth()->id();
            $red->save();

            $statusText = $red->status ? 'activada' : 'desactivada';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Red de conocimiento {$statusText} correctamente",
            ]);

            if ($this->showShowModal && $this->selectedRed && $this->selectedRed->id == $redId) {
                $this->selectedRed->refresh();
                $this->dispatch('refreshModal');
            }
        }
    }
}
