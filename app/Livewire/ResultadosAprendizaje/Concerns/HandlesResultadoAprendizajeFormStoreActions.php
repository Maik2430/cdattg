<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;

trait HandlesResultadoAprendizajeFormStoreActions
{
    use HandlesResultadoAprendizajeFormDuracionHelpers;

    private function storeResultadoAprendizaje(): void
    {
        $resultado = ResultadosAprendizaje::create([
            'codigo' => strtoupper($this->codigo),
            'nombre' => $this->nombre,
            'duracion' => $this->duracion,
            'status' => $this->status,
            'user_create_id' => auth()->id(),
            'user_edit_id' => auth()->id(),
        ]);

        if ($this->competencia_id) {
            $competencia = Competencia::find($this->competencia_id);
            if ($competencia) {
                $resultado->competencias()->attach($this->competencia_id, [
                    'user_create_id' => auth()->id(),
                    'user_edit_id' => auth()->id(),
                ]);

                $this->redistribuirDuracionCompetencia($competencia);
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Resultado de aprendizaje creado correctamente',
        ]);
        $this->dispatch('resultadoCreado');
    }
}
