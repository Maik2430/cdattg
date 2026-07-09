<?php

namespace App\Services\Concerns\JornadaValidation;

trait HandlesJornadaValidationTardanzaActions
{
    public function validarLlegadaTarde($horaIngreso, string $jornada): array
    {
        $horarios = $this->obtenerHorariosJornada($jornada);

        if (! $horarios) {
            return ['llego_tarde' => false, 'minutos_retraso' => 0];
        }

        $horaIngresoCarbon = $this->parsearHora($horaIngreso);
        $horaInicioJornada = $this->parsearHora($horarios['inicio']);
        $tolerancia = $horarios['tolerancia_entrada'] ?? 15;

        $horaLimiteTolerance = $horaInicioJornada->copy()->addMinutes($tolerancia);

        $llegoTarde = $horaIngresoCarbon->greaterThan($horaLimiteTolerance);

        if ($llegoTarde) {
            $minutosRetraso = $horaIngresoCarbon->diffInMinutes($horaInicioJornada, false);
            $minutosRetraso = abs($minutosRetraso);
        } else {
            $minutosRetraso = 0;
        }

        return [
            'llego_tarde' => $llegoTarde,
            'minutos_retraso' => $minutosRetraso,
            'hora_limite' => $horaLimiteTolerance->format('H:i:s'),
        ];
    }

    public function validarSalidaTemprana($horaSalida, string $jornada): array
    {
        $horarios = $this->obtenerHorariosJornada($jornada);

        if (! $horarios) {
            return ['salio_temprano' => false, 'minutos_anticipado' => 0];
        }

        $horaSalidaCarbon = $this->parsearHora($horaSalida);
        $horaFinJornada = $this->parsearHora($horarios['fin']);
        $tolerancia = $horarios['tolerancia_salida'] ?? 10;

        $horaLimiteTolerance = $horaFinJornada->copy()->subMinutes($tolerancia);

        $salioTemprano = $horaSalidaCarbon->lessThan($horaLimiteTolerance);
        $minutosAnticipado = $salioTemprano ? abs($horaFinJornada->diffInMinutes($horaSalidaCarbon)) : 0;

        return [
            'salio_temprano' => $salioTemprano,
            'minutos_anticipado' => $minutosAnticipado,
            'hora_limite' => $horaLimiteTolerance->format('H:i:s'),
        ];
    }

    public function generarNovedadEntrada($horaIngreso, string $jornada): string
    {
        $validacion = $this->validarLlegadaTarde($horaIngreso, $jornada);

        if (! $validacion['llego_tarde']) {
            return 'Puntual';
        }

        $minutos = $validacion['minutos_retraso'];

        return match (true) {
            $minutos <= 15 => 'Tarde',
            $minutos <= 30 => 'Muy tarde',
            default => 'Falta justificada',
        };
    }

    public function generarNovedadSalida($horaSalida, string $jornada): string
    {
        $validacion = $this->validarSalidaTemprana($horaSalida, $jornada);

        if (! $validacion['salio_temprano']) {
            return 'Normal';
        }

        return 'Anticipada';
    }
}
