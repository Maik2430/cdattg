<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\Competencia;

trait HandlesResultadoAprendizajeIndexCompetenciasAssignActions
{
    public function asignarCompetencia($competenciaId)
    {
        if (! $this->selectedResultado) {
            return;
        }

        $competencia = Competencia::find($competenciaId);
        if (! $competencia) {
            return;
        }

        if (! $this->selectedResultado->competencias()->where('competencias.id', $competenciaId)->exists()) {
            $this->selectedResultado->competencias()->attach($competenciaId, [
                'user_create_id' => auth()->id(),
                'user_edit_id' => auth()->id(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Competencia asignada correctamente',
            ]);
        }

        $this->refreshCompetencias();
    }

    public function asignarSeleccionadas()
    {
        if (! $this->selectedResultado || empty($this->competenciasSeleccionadas)) {
            return;
        }

        foreach ($this->competenciasSeleccionadas as $competenciaId) {
            if (! $this->selectedResultado->competencias()->where('competencias.id', $competenciaId)->exists()) {
                $this->selectedResultado->competencias()->attach($competenciaId, [
                    'user_create_id' => auth()->id(),
                    'user_edit_id' => auth()->id(),
                ]);
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($this->competenciasSeleccionadas).' competencias asignadas correctamente',
        ]);

        $this->refreshCompetencias();
    }
}
