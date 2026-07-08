<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;
use Illuminate\Support\Facades\Log;

trait HandlesGuiaAprendizajeGestionarResultadosDesassignActions
{
    public function desasignarResultado($resultadoId)
    {
        try {
            $resultado = ResultadosAprendizaje::find($resultadoId);

            if (! $resultado) {
                return;
            }

            $this->guia->resultadosAprendizaje()->detach($resultadoId);
            $this->cargarResultados();
        } catch (\Exception $e) {
            Log::error('Error en desasignarResultado: '.$e->getMessage());
        }
    }

    public function desasociarResultado($resultadoId)
    {
        try {
            $resultado = ResultadosAprendizaje::find($resultadoId);

            if (! $resultado) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Resultado de aprendizaje no encontrado',
                ]);

                return;
            }

            $this->guia->resultadosAprendizaje()->detach($resultadoId);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Resultado '{$resultado->codigo}' desasignado correctamente",
            ]);

            $this->cargarResultados();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al desasignar resultado: '.$e->getMessage(),
            ]);
        }
    }
}
