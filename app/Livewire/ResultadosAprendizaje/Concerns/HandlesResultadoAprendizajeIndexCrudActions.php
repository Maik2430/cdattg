<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesResultadoAprendizajeIndexCrudActions
{
    public function deleteResultado($resultadoId)
    {
        $resultado = ResultadosAprendizaje::with(['guiasAprendizaje', 'competencias'])->find($resultadoId);

        if (! $resultado) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Resultado de aprendizaje no encontrado',
            ]);

            return;
        }

        if ($resultado->guiasAprendizaje->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar el resultado. Tiene '.$resultado->guiasAprendizaje->count().' guía(s) asociada(s).',
            ]);

            return;
        }

        try {
            $codigo = $resultado->codigo;

            $resultado->competencias()->detach();

            $resultado->delete();
            $this->closeDeleteModal();
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => "Resultado de aprendizaje '{$codigo}' eliminado correctamente",
            ]);
            $this->dispatch('resultadoEliminado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar el resultado: '.$e->getMessage(),
            ]);
        }
    }

    public function toggleStatus($resultadoId)
    {
        $resultado = ResultadosAprendizaje::find($resultadoId);

        if ($resultado) {
            $resultado->status = ! $resultado->status;
            $resultado->user_edit_id = auth()->id();
            $resultado->save();

            $statusText = $resultado->status ? 'activado' : 'desactivado';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Resultado de aprendizaje {$statusText} correctamente",
            ]);

            if ($this->showShowModal && $this->selectedResultado && $this->selectedResultado->id == $resultadoId) {
                $this->selectedResultado->refresh();
                $this->dispatch('refreshModal');
            }
        }
    }
}
