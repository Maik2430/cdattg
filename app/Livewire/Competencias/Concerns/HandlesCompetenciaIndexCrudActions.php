<?php

namespace App\Livewire\Competencias\Concerns;

use App\Models\Competencia;

trait HandlesCompetenciaIndexCrudActions
{
    public function deleteCompetencia($competenciaId)
    {
        $competencia = Competencia::with(['programasFormacion'])->find($competenciaId);

        if (! $competencia) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Competencia no encontrada',
            ]);

            return;
        }

        if ($competencia->programasFormacion->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar la competencia. Tiene '.$competencia->programasFormacion->count().' programas asociados.',
            ]);

            return;
        }

        try {
            $competencia->delete();
            $this->closeDeleteModal();
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Competencia eliminada correctamente',
            ]);
            $this->dispatch('competenciaEliminada');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar la competencia: '.$e->getMessage(),
            ]);
        }
    }

    public function toggleStatus($competenciaId)
    {
        $competencia = Competencia::find($competenciaId);

        if ($competencia) {
            $competencia->status = ! $competencia->status;
            $competencia->user_edit_id = auth()->id();
            $competencia->save();

            $statusText = $competencia->status ? 'activada' : 'desactivada';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Competencia {$statusText} correctamente",
            ]);

            if ($this->showShowModal && $this->selectedCompetencia && $this->selectedCompetencia->id == $competenciaId) {
                $this->selectedCompetencia->refresh();
                $this->dispatch('refreshModal');
            }
        }
    }
}
