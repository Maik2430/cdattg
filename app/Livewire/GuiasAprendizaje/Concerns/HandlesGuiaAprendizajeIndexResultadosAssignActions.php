<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesGuiaAprendizajeIndexResultadosAssignActions
{
    public function asignarResultado($resultadoId)
    {
        if (! $this->selectedGuia) {
            return;
        }

        $resultado = ResultadosAprendizaje::find($resultadoId);
        if (! $resultado) {
            return;
        }

        if (! $this->selectedGuia->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultadoId)->exists()) {
            $this->selectedGuia->resultadosAprendizaje()->attach($resultadoId, [
                'user_create_id' => auth()->id(),
                'user_edit_id' => auth()->id(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Resultado de aprendizaje asignado correctamente',
            ]);
        }

        $this->refreshResultados();
    }

    public function asignarSeleccionados()
    {
        if (! $this->selectedGuia || empty($this->resultadosSeleccionados)) {
            return;
        }

        foreach ($this->resultadosSeleccionados as $resultadoId) {
            if (! $this->selectedGuia->resultadosAprendizaje()->where('resultados_aprendizajes.id', $resultadoId)->exists()) {
                $this->selectedGuia->resultadosAprendizaje()->attach($resultadoId, [
                    'user_create_id' => auth()->id(),
                    'user_edit_id' => auth()->id(),
                ]);
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($this->resultadosSeleccionados).' resultados asignados correctamente',
        ]);

        $this->refreshResultados();
    }
}
