<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

use App\Models\FichaDiasFormacion;

trait HandlesFichaFormLoadDataHelpers
{
    private function loadFichaData(): void
    {
        if (! $this->ficha) {
            return;
        }

        $this->ficha_codigo = $this->ficha->ficha;
        $this->programa_formacion_id = $this->ficha->programa_formacion_id;
        $this->sede_id = $this->ficha->sede_id;
        $this->instructor_id = $this->ficha->instructor_id;
        $this->ambiente_id = $this->ficha->ambiente_id;

        $this->fecha_inicio = $this->ficha->fecha_inicio
            ? $this->ficha->fecha_inicio->format('Y-m-d')
            : null;
        $this->fecha_fin = $this->ficha->fecha_fin
            ? $this->ficha->fecha_fin->format('Y-m-d')
            : null;

        $this->modalidad_formacion_id = $this->ficha->modalidad_formacion_id;
        $this->jornada_id = $this->ficha->jornada_id;
        $this->total_horas = $this->ficha->total_horas;

        $this->dias_formacion = FichaDiasFormacion::where('ficha_id', $this->ficha->id)
            ->pluck('dia_id')
            ->toArray();

        $this->status = $this->ficha->status;
    }
}
