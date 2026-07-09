<?php

namespace App\Services\Concerns\JornadaValidation;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

trait HandlesJornadaValidationConsultaHelpers
{
    public function obtenerHorariosJornada(string $jornada): ?array
    {
        $jornadas = Config::get('jornadas.horarios', []);

        return $jornadas[$jornada] ?? null;
    }

    public function obtenerTodasLasJornadas(): array
    {
        return array_keys(Config::get('jornadas.horarios', []));
    }

    private function parsearHora($hora): Carbon
    {
        if ($hora instanceof Carbon) {
            return $hora->copy();
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $hora)) {
            [$h, $m, $s] = explode(':', $hora);

            return Carbon::createFromTime((int) $h, (int) $m, (int) $s);
        }

        return Carbon::parse($hora);
    }
}
