<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

trait HandlesResultadoAprendizajeIndexCompetenciasDesassignActions
{
    public function desasignarCompetencia($competenciaId)
    {
        if (! $this->selectedResultado) {
            return;
        }

        $this->selectedResultado->competencias()->detach($competenciaId);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Competencia desasignada correctamente',
        ]);

        $this->refreshCompetencias();
    }

    public function desasignarTodas()
    {
        if (! $this->selectedResultado) {
            return;
        }

        $competenciasCount = $this->selectedResultado->competencias->count();
        $this->selectedResultado->competencias()->detach();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $competenciasCount.' competencias desasignadas correctamente',
        ]);

        $this->refreshCompetencias();
    }
}
