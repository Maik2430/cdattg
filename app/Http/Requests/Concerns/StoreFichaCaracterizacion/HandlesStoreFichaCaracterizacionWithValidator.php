<?php

namespace App\Http\Requests\Concerns\StoreFichaCaracterizacion;

use App\Models\ParametroTema;

trait HandlesStoreFichaCaracterizacionWithValidator
{
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            \Log::info('StoreFichaCaracterizacionRequest withValidator called', [
                'user_id' => $this->user()->id,
                'errors_count' => $validator->errors()->count(),
                'errors' => $validator->errors()->toArray(),
            ]);

            $this->validarJornadaTema($validator);
            $this->validarDisponibilidadRecursos($validator);
            $this->validarFichaUnica($validator);
            $this->validarDiasFormacionEnRango($validator);
            $this->validarReglasNegocio($validator);

            \Log::info('StoreFichaCaracterizacionRequest withValidator completed', [
                'user_id' => $this->user()->id,
                'final_errors_count' => $validator->errors()->count(),
                'final_errors' => $validator->errors()->toArray(),
            ]);
        });
    }

    private function validarJornadaTema($validator): void
    {
        if (! $this->jornada_id) {
            return;
        }

        $jornadaParametroTema = ParametroTema::where('id', $this->jornada_id)
            ->whereHas('tema', function ($q) {
                $q->where('name', 'LIKE', '%JORNADA%');
            })
            ->first();

        if (! $jornadaParametroTema) {
            $validator->errors()->add('jornada_id', 'La jornada seleccionada no pertenece al tema JORNADA.');
        }
    }

    private function validarDisponibilidadRecursos($validator): void
    {
        if ($this->ambiente_id && $this->fecha_inicio && $this->fecha_fin) {
            $validacionAmbiente = $this->validarDisponibilidadAmbiente(
                $this->ambiente_id,
                $this->fecha_inicio,
                $this->fecha_fin
            );

            if (! $validacionAmbiente['valido']) {
                $validator->errors()->add('ambiente_id', $validacionAmbiente['mensaje']);
            }
        }

        if ($this->instructor_id && $this->fecha_inicio && $this->fecha_fin) {
            $validacionInstructor = $this->validarDisponibilidadInstructor(
                $this->instructor_id,
                $this->fecha_inicio,
                $this->fecha_fin
            );

            if (! $validacionInstructor['valido']) {
                $validator->errors()->add('instructor_id', $validacionInstructor['mensaje']);
            }
        }
    }

    private function validarFichaUnica($validator): void
    {
        if (! $this->ficha || ! $this->programa_formacion_id) {
            return;
        }

        $validacionFicha = $this->validarFichaUnicaPorPrograma(
            $this->ficha,
            $this->programa_formacion_id
        );

        if (! $validacionFicha['valido']) {
            $validator->errors()->add('ficha', $validacionFicha['mensaje']);
        }
    }
}
