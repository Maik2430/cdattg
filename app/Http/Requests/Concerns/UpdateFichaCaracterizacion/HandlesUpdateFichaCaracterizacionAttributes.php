<?php

namespace App\Http\Requests\Concerns\UpdateFichaCaracterizacion;

trait HandlesUpdateFichaCaracterizacionAttributes
{
    public function attributes(): array
    {
        return [
            'ficha' => 'número de ficha',
            'programa_formacion_id' => 'programa de formación',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'instructor_id' => 'instructor',
            'ambiente_id' => 'ambiente',
            'modalidad_formacion_id' => 'modalidad de formación',
            'sede_id' => 'sede',
            'jornada_id' => 'jornada',
            'total_horas' => 'total de horas',
            'status' => 'estado',
        ];
    }
}
