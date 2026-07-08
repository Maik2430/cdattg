<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

trait HandlesGuiaAprendizajeIndexResultadosDesassignActions
{
    public function desasignarResultado($resultadoId)
    {
        if (! $this->selectedGuia) {
            return;
        }

        $this->selectedGuia->resultadosAprendizaje()->detach($resultadoId);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Resultado de aprendizaje desasignado correctamente',
        ]);

        $this->refreshResultados();
    }

    public function desasignarTodos()
    {
        if (! $this->selectedGuia) {
            return;
        }

        $resultadosCount = $this->selectedGuia->resultadosAprendizaje->count();
        $this->selectedGuia->resultadosAprendizaje()->detach();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $resultadosCount.' resultados desasignados correctamente',
        ]);

        $this->refreshResultados();
    }
}
