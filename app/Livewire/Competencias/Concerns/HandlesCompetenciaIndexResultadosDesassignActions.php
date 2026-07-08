<?php

namespace App\Livewire\Competencias\Concerns;

trait HandlesCompetenciaIndexResultadosDesassignActions
{
    public function desasignarResultado($resultadoId)
    {
        if (! $this->selectedCompetencia) {
            return;
        }

        try {
            if (! $this->selectedCompetencia->resultadosAprendizaje->contains($resultadoId)) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'Este resultado no está asignado a la competencia',
                ]);

                return;
            }

            $this->selectedCompetencia->resultadosAprendizaje()->detach($resultadoId);

            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Resultado desasignado correctamente',
            ]);

            $this->selectedCompetencia->refresh();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al desasignar resultado: '.$e->getMessage(),
            ]);
        }
    }

    public function desasignarTodos()
    {
        if (! $this->selectedCompetencia) {
            return;
        }

        try {
            $count = $this->selectedCompetencia->resultadosAprendizaje->count();

            if ($count === 0) {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'message' => 'No hay resultados asignados para desasignar',
                ]);

                return;
            }

            $this->selectedCompetencia->resultadosAprendizaje()->detach();

            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => "Todos los {$count} resultados han sido desasignados",
            ]);

            $this->selectedCompetencia->refresh();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al desasignar resultados: '.$e->getMessage(),
            ]);
        }
    }
}
