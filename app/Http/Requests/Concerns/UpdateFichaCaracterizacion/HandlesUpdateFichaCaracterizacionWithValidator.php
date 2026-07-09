<?php

namespace App\Http\Requests\Concerns\UpdateFichaCaracterizacion;

use App\Models\ParametroTema;

trait HandlesUpdateFichaCaracterizacionWithValidator
{
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $fichaId = $this->route('fichaCaracterizacion') ?? $this->route('id');

            $this->validarJornadaTema($validator);
            $this->validarDisponibilidadRecursos($validator, $fichaId);
            $this->validarFichaUnica($validator, $fichaId);
            $this->validarReglasNegocio($validator, $fichaId);
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

    private function validarDisponibilidadRecursos($validator, $fichaId): void
    {
        if ($this->ambiente_id && $this->fecha_inicio && $this->fecha_fin) {
            $validacionAmbiente = $this->validarDisponibilidadAmbiente(
                $this->ambiente_id,
                $this->fecha_inicio,
                $this->fecha_fin,
                $fichaId
            );

            if (! $validacionAmbiente['valido']) {
                $validator->errors()->add('ambiente_id', $validacionAmbiente['mensaje']);
            }
        }

        if ($this->instructor_id && $this->fecha_inicio && $this->fecha_fin) {
            $validacionInstructor = $this->validarDisponibilidadInstructor(
                $this->instructor_id,
                $this->fecha_inicio,
                $this->fecha_fin,
                $fichaId
            );

            if (! $validacionInstructor['valido']) {
                $validator->errors()->add('instructor_id', $validacionInstructor['mensaje']);
            }
        }
    }

    private function validarFichaUnica($validator, $fichaId): void
    {
        if (! $this->ficha || ! $this->programa_formacion_id) {
            return;
        }

        $validacionFicha = $this->validarFichaUnicaPorPrograma(
            $this->ficha,
            $this->programa_formacion_id,
            $fichaId
        );

        if (! $validacionFicha['valido']) {
            $validator->errors()->add('ficha', $validacionFicha['mensaje']);
        }
    }

    private function validarReglasNegocio($validator, $fichaId): void
    {
        $datos = $this->all();
        $validacionReglas = $this->validarReglasNegocioSena($datos, $fichaId);

        if ($validacionReglas['valido']) {
            return;
        }

        $mensajes = explode('. ', $validacionReglas['mensaje']);
        foreach ($mensajes as $mensaje) {
            $mensaje = trim($mensaje);
            if (empty($mensaje)) {
                continue;
            }

            if (strpos($mensaje, 'fecha de inicio') !== false) {
                $validator->errors()->add('fecha_inicio', $mensaje);
            } elseif (strpos($mensaje, 'fecha de fin') !== false) {
                $validator->errors()->add('fecha_fin', $mensaje);
            } elseif (strpos($mensaje, 'duración') !== false) {
                $validator->errors()->add('fecha_fin', $mensaje);
            } elseif (strpos($mensaje, 'ambiente') !== false && strpos($mensaje, 'sede') !== false) {
                $validator->errors()->add('ambiente_id', $mensaje);
            } else {
                $validator->errors()->add('reglas_negocio', $mensaje);
            }
        }
    }
}
