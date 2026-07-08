<?php

namespace App\Livewire\GuiasAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;
use Illuminate\Support\Facades\Log;

trait HandlesGuiaAprendizajeGestionarResultadosAssignActions
{
    public function asignarResultadoDirecto($resultadoId)
    {
        try {
            $resultado = ResultadosAprendizaje::find($resultadoId);

            if (! $resultado) {
                return;
            }

            $yaAsignado = $this->guia->resultadosAprendizaje()
                ->where('resultados_aprendizajes.id', $resultadoId)
                ->exists();

            if ($yaAsignado) {
                return;
            }

            $this->guia->resultadosAprendizaje()->attach($resultadoId, [
                'user_create_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->cargarResultados();
        } catch (\Exception $e) {
            Log::error('Error en asignarResultadoDirecto: '.$e->getMessage());
        }
    }

    public function openAsignarModal()
    {
        $this->showAsignarModal = true;
        $this->resultadoSeleccionado = null;
    }

    public function closeAsignarModal()
    {
        $this->showAsignarModal = false;
        $this->resultadoSeleccionado = null;
    }

    public function asignarResultado()
    {
        $this->validate([
            'resultadoSeleccionado' => 'required|exists:resultados_aprendizajes,id',
        ]);

        $this->asignarResultadoDirecto($this->resultadoSeleccionado);
        $this->closeAsignarModal();
    }
}
