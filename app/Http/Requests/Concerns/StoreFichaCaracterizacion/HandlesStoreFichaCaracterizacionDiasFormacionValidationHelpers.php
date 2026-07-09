<?php

namespace App\Http\Requests\Concerns\StoreFichaCaracterizacion;

use Carbon\Carbon;

trait HandlesStoreFichaCaracterizacionDiasFormacionValidationHelpers
{
    private function validarDiasFormacionEnRango($validator): void
    {
        if (! $this->fecha_inicio || ! $this->fecha_fin || ! $this->dias_formacion || ! is_array($this->dias_formacion)) {
            return;
        }

        $fechaInicio = Carbon::parse($this->fecha_inicio);
        $fechaFin = Carbon::parse($this->fecha_fin);

        $mapeoDias = [
            12 => 1,
            13 => 2,
            14 => 3,
            15 => 4,
            16 => 5,
            17 => 6,
            18 => 0,
        ];

        $diasEnRango = [];
        $fechaActual = $fechaInicio->copy();
        while ($fechaActual->lte($fechaFin)) {
            $diaSemana = $fechaActual->dayOfWeek;
            if (! in_array($diaSemana, $diasEnRango)) {
                $diasEnRango[] = $diaSemana;
            }
            $fechaActual->addDay();
        }

        $diasInvalidos = [];
        foreach ($this->dias_formacion as $diaId) {
            $diaId = (int) $diaId;
            if (isset($mapeoDias[$diaId])) {
                $diaSemana = $mapeoDias[$diaId];
                if (! in_array($diaSemana, $diasEnRango)) {
                    $diasInvalidos[] = $diaId;
                }
            }
        }

        if (empty($diasInvalidos)) {
            return;
        }

        $nombresDias = [
            12 => 'LUNES',
            13 => 'MARTES',
            14 => 'MIÉRCOLES',
            15 => 'JUEVES',
            16 => 'VIERNES',
            17 => 'SÁBADO',
            18 => 'DOMINGO',
        ];
        $nombresInvalidos = array_map(function ($id) use ($nombresDias) {
            return $nombresDias[$id] ?? "Día ID {$id}";
        }, $diasInvalidos);

        $validator->errors()->add(
            'dias_formacion',
            'Los días '.implode(', ', $nombresInvalidos).' no están dentro del rango de fechas seleccionado ('.$fechaInicio->format('d/m/Y').' a '.$fechaFin->format('d/m/Y').').'
        );
    }

    private function validarReglasNegocio($validator): void
    {
        $datos = $this->all();
        $validacionReglas = $this->validarReglasNegocioSena($datos);

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
