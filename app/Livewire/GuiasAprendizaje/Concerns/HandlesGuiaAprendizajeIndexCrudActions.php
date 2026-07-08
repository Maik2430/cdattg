<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\GuiasAprendizaje;

trait HandlesGuiaAprendizajeIndexCrudActions
{
    public function deleteGuia($guiaId)
    {
        $guia = GuiasAprendizaje::with(['actividades', 'resultadosAprendizaje'])->find($guiaId);

        if (! $guia) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Guía de aprendizaje no encontrada',
            ]);

            return;
        }

        if ($guia->actividades->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No se puede eliminar la guía. Tiene '.$guia->actividades->count().' actividad(es) asociada(s).',
            ]);

            return;
        }

        try {
            $codigo = $guia->codigo;

            $guia->resultadosAprendizaje()->detach();

            $guia->delete();
            $this->closeDeleteModal();
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => "Guía de aprendizaje '{$codigo}' eliminada correctamente",
            ]);
            $this->dispatch('guiaEliminada');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al eliminar la guía: '.$e->getMessage(),
            ]);
        }
    }

    public function toggleStatus($guiaId)
    {
        $guia = GuiasAprendizaje::find($guiaId);

        if ($guia) {
            $guia->status = ! $guia->status;
            $guia->user_edit_id = auth()->id();
            $guia->save();

            $statusText = $guia->status ? 'activada' : 'desactivada';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Guía de aprendizaje {$statusText} correctamente",
            ]);

            if ($this->showShowModal && $this->selectedGuia && $this->selectedGuia->id == $guiaId) {
                $this->selectedGuia->refresh();
                $this->dispatch('refreshModal');
            }
        }
    }

    public function handleConfirmedAction($action, $params)
    {
        try {
            switch ($action) {
                case 'eliminarGuia':
                    $this->deleteGuia($params);
                    break;
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Acción confirmada exitosamente',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al ejecutar la acción: '.$e->getMessage(),
            ]);
        }
    }
}
