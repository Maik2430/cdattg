<?php

namespace App\Services\Concerns\JornadaValidation;

use Illuminate\Support\Facades\Config;

trait HandlesJornadaValidationHorarioActions
{
    public function validarHorarioJornada($hora, string $jornada): bool
    {
        $horarios = $this->obtenerHorariosJornada($jornada);

        if (! $horarios) {
            return false;
        }

        $horaValidar = $this->parsearHora($hora);
        $horaInicio = $this->parsearHora($horarios['inicio']);
        $horaFin = $this->parsearHora($horarios['fin']);

        return $horaValidar->between($horaInicio, $horaFin);
    }

    public function validarAsistenciaEnJornada($horaIngreso, $horaActual, string $jornada): bool
    {
        $horarios = $this->obtenerHorariosJornada($jornada);

        if (! $horarios) {
            return false;
        }

        $horaIngresoCarbon = $this->parsearHora($horaIngreso);
        $horaActualCarbon = $this->parsearHora($horaActual);
        $horaInicio = $this->parsearHora($horarios['inicio']);
        $horaFin = $this->parsearHora($horarios['fin']);

        return $horaIngresoCarbon->between($horaInicio, $horaFin)
            && $horaActualCarbon->between($horaInicio, $horaFin);
    }

    public function obtenerJornadaPorHora($hora): ?string
    {
        $horaCarbon = $this->parsearHora($hora);
        $jornadas = Config::get('jornadas.horarios', []);

        foreach ($jornadas as $nombreJornada => $horarios) {
            $horaInicio = $this->parsearHora($horarios['inicio']);
            $horaFin = $this->parsearHora($horarios['fin']);

            if ($horaCarbon->between($horaInicio, $horaFin)) {
                return $nombreJornada;
            }
        }

        return null;
    }

    public function tienetiempoMinimoClase($horaIngreso, $horaSalida): bool
    {
        $tiempoMinimo = Config::get('jornadas.validacion.tiempo_minimo_clase', 45);

        $ingreso = $this->parsearHora($horaIngreso);
        $salida = $this->parsearHora($horaSalida);

        $diferencia = $salida->diffInMinutes($ingreso);

        return $diferencia >= $tiempoMinimo;
    }
}
