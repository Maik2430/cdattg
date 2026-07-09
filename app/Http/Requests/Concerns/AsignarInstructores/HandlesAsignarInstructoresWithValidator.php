<?php

namespace App\Http\Requests\Concerns\AsignarInstructores;

trait HandlesAsignarInstructoresWithValidator
{
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validarConflictosFechas($validator);
            $this->validarEspecialidadesRequeridas($validator);
            $this->validarDisponibilidadHoraria($validator);
            $this->validarReglasSENA($validator);
            $this->validarCoherenciaHorasCompetencia($validator);
        });
    }
}
