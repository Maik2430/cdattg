<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

trait HandlesFichaFormResetActions
{
    public function resetForm(): void
    {
        $this->reset([
            'ficha_codigo',
            'programa_formacion_id',
            'sede_id',
            'instructor_id',
            'ambiente_id',
            'fecha_inicio',
            'fecha_fin',
            'modalidad_formacion_id',
            'jornada_id',
            'total_horas',
            'dias_formacion',
            'status',
        ]);

        $this->status = 1;
        $this->dias_formacion = [];
    }
}
