<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

use App\Models\FichaCaracterizacion;
use Carbon\Carbon;

trait HandlesAsignarInstructoresConflictFechasHelpers
{
    private function validarConflictosFechas($validator): void
    {
        $instructores = $this->input('instructores', []);
        $fichaId = $this->route('id');
        $ficha = FichaCaracterizacion::find($fichaId);
        $jornadaIdFicha = $ficha ? $ficha->jornada_id : null;

        foreach ($instructores as $index => $instructorData) {
            $instructorId = $instructorData['instructor_id'];
            $fechaInicio = Carbon::parse($instructorData['fecha_inicio']);
            $fechaFin = Carbon::parse($instructorData['fecha_fin']);

            $diasNuevos = [];
            if (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
                $diasNuevos = array_keys($instructorData['dias']);
            } elseif (isset($instructorData['dias_semana']) && is_array($instructorData['dias_semana'])) {
                $diasNuevos = $instructorData['dias_semana'];
            } elseif (isset($instructorData['dias_formacion']) && is_array($instructorData['dias_formacion'])) {
                $diasNuevos = collect($instructorData['dias_formacion'])->pluck('dia_id')->filter()->toArray();
            }

            $this->validarConflictosOtrosInstructor($validator, $instructorId, $fechaInicio, $fechaFin, $diasNuevos, $jornadaIdFicha, $index);
            $this->validarConflictosMismaFicha($validator, $instructores, $index, $instructorId, $fechaInicio, $fechaFin, $diasNuevos);
        }
    }
}
