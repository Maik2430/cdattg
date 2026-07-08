<?php

namespace App\Livewire\Competencias\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesCompetenciaIndexResultadosAssignActions
{
    public function asignarResultado($resultadoId)
    {
        if (! $this->selectedCompetencia) {
            return;
        }

        try {
            $resultado = ResultadosAprendizaje::activos()->find($resultadoId);
            if (! $resultado) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Resultado de aprendizaje no encontrado o inactivo',
                ]);

                return;
            }

            if ($this->selectedCompetencia->resultadosAprendizaje->contains($resultadoId)) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'Este resultado ya está asignado a la competencia',
                ]);

                return;
            }

            $this->selectedCompetencia->resultadosAprendizaje()->attach($resultadoId, [
                'duracion' => $resultado->duracion ?? 0,
                'user_create_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Resultado asignado correctamente',
            ]);

            $this->selectedCompetencia->refresh();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al asignar resultado: '.$e->getMessage(),
            ]);
        }
    }

    public function asignarSeleccionados()
    {
        if (! $this->selectedCompetencia || empty($this->resultadosSeleccionados)) {
            return;
        }

        try {
            $count = 0;
            $yaAsignados = 0;

            foreach ($this->resultadosSeleccionados as $resultadoId) {
                $resultado = ResultadosAprendizaje::activos()->find($resultadoId);
                if (! $resultado) {
                    continue;
                }

                if ($this->selectedCompetencia->resultadosAprendizaje->contains($resultadoId)) {
                    $yaAsignados++;

                    continue;
                }

                $this->selectedCompetencia->resultadosAprendizaje()->attach($resultadoId, [
                    'duracion' => $resultado->duracion ?? 0,
                    'user_create_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
            }

            $message = $count > 0
                ? "{$count} resultados asignados correctamente"
                : 'Todos los resultados seleccionados ya estaban asignados';

            if ($yaAsignados > 0 && $count > 0) {
                $message .= " ({$yaAsignados} ya estaban asignados)";
            }

            $this->dispatch('notify', [
                'type' => $count > 0 ? 'success' : 'info',
                'message' => $message,
            ]);

            $this->resultadosSeleccionados = [];
            $this->selectAll = false;
            $this->selectedCompetencia->refresh();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al asignar resultados: '.$e->getMessage(),
            ]);
        }
    }
}
