<?php

namespace App\Livewire\ResultadosAprendizaje\Concerns;

use App\Models\Competencia;
use App\Models\ResultadosAprendizaje;

trait HandlesResultadoAprendizajeFormUpdateActions
{
    use HandlesResultadoAprendizajeFormDuracionHelpers;

    private function updateResultadoAprendizaje(): bool
    {
        $resultado = ResultadosAprendizaje::find($this->resultadoId);

        if (! $resultado) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Resultado de aprendizaje no encontrado',
            ]);

            return false;
        }

        $resultado->update([
            'codigo' => strtoupper($this->codigo),
            'nombre' => $this->nombre,
            'duracion' => $this->duracion,
            'status' => $this->status,
            'user_edit_id' => auth()->id(),
        ]);

        if ($this->competencia_id) {
            $competencia = Competencia::find($this->competencia_id);
            if ($competencia) {
                if (! $resultado->competencias()->where('competencias.id', $this->competencia_id)->exists()) {
                    $resultado->competencias()->attach($this->competencia_id, [
                        'user_create_id' => auth()->id(),
                        'user_edit_id' => auth()->id(),
                    ]);

                    $this->redistribuirDuracionCompetencia($competencia);
                }
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Resultado de aprendizaje actualizado correctamente',
        ]);
        $this->dispatch('resultadoActualizado');

        return true;
    }
}
