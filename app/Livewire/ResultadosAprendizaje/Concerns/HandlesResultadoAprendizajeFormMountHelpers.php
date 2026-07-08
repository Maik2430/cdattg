<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\ResultadosAprendizaje;

trait HandlesResultadoAprendizajeFormMountHelpers
{
    public function mount($isEdit = false, $resultadoId = null)
    {
        $this->isEdit = $isEdit;
        $this->resultadoId = $resultadoId;
        $this->status = true;

        if ($isEdit && $resultadoId) {
            $this->loadResultado($resultadoId);
        }
    }

    public function loadResultado($resultadoId)
    {
        $resultado = ResultadosAprendizaje::with('competencias')->find($resultadoId);

        if (! $resultado) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Resultado de aprendizaje no encontrado',
            ]);

            return;
        }

        $this->codigo = $resultado->codigo;
        $this->nombre = $resultado->nombre;
        $this->duracion = $resultado->duracion;

        $primeraCompetencia = $resultado->competencias->first();
        $this->competencia_id = $primeraCompetencia ? $primeraCompetencia->id : null;

        $this->status = $resultado->status;
    }
}
