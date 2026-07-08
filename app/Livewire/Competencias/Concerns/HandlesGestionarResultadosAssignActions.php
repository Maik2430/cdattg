<?php

namespace App\Livewire\Competencias\Concerns;

trait HandlesGestionarResultadosAssignActions
{
    public function asociarResultados()
    {
        if (empty($this->selectedResultados)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Debe seleccionar al menos un resultado de aprendizaje',
            ]);

            return;
        }

        try {
            foreach ($this->selectedResultados as $resultadoId) {
                $this->competencia->resultadosAprendizaje()->attach($resultadoId, [
                    'duracion' => 0,
                    'user_create_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->closeAsociarModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => count($this->selectedResultados).' resultado(s) asociado(s) correctamente',
            ]);
            $this->dispatch('resultadoAsociado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al asociar resultados: '.$e->getMessage(),
            ]);
        }
    }

    public function desasociarResultado($resultadoId)
    {
        try {
            $this->competencia->resultadosAprendizaje()->detach($resultadoId);

            $this->closeDesasociarModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Resultado desasociado correctamente',
            ]);
            $this->dispatch('resultadoDesasociado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al desasociar resultado: '.$e->getMessage(),
            ]);
        }
    }

    public function asociarResultadoDirecto($resultadoId)
    {
        try {
            $this->competencia->resultadosAprendizaje()->attach($resultadoId, [
                'duracion' => 0,
                'user_create_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Resultado asociado correctamente',
            ]);
            $this->dispatch('resultadoAsociado');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al asociar resultado: '.$e->getMessage(),
            ]);
        }
    }
}
