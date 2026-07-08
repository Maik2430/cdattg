<?php

namespace App\Http\Controllers\Concerns\QrAsistence;

use App\Models\AsistenciaAprendiz;
use Carbon\Carbon;
use Carbon\Month;
use Carbon\WeekDay;
use DateTimeInterface;

trait HandlesQrAsistenceWebListActions
{
    public function getAsistenceWebList(string $ficha, string $jornada)
    {
        $horaEjecucion = Carbon::now()->format('H:i:s');
        $fechaActual = Carbon::now()->format('Y-m-d');

        $hI = 8;
        $mI = 0;
        $h2I = 12;
        $m2F = 0;

        $asistencias = AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($ficha, $jornada): void {
            $query->whereHas('ficha', function ($query) use ($ficha): void {
                $query->where('ficha', $ficha);
            })->whereHas('jornada', function ($query) use ($jornada): void {
                $query->where('jornada', $jornada);
            });
        })->whereDate('created_at', $fechaActual)->get();

        if ($asistencias->isEmpty()) {
            return back()->with('error', 'No se encontraron asistencias para la ficha y jornada proporcionadas');
        }

        foreach ($asistencias as $asistencia) {
            $hourEnter = Carbon::parse($asistencia->hora_ingreso)->format('H:i:s');
            $dateEnter = Carbon::parse($asistencia->created_at)->format('Y-m-d');

            if ($this->validateHour($horaEjecucion, $hI, $mI, $h2I, $m2F) && $dateEnter == $fechaActual) {
                return view('qr_asistence.showList', compact('asistencias', 'ficha'));
            }
        }

        return back()->with('error', 'No se encontraron asistencias válidas para la ficha y jornada proporcionadas');
    }

    public function validateHour(DateTimeInterface|WeekDay|Month|string|int|float|null $ingreso, $hora1, $min1, $hora2, $min2): bool
    {
        $horaInicio = Carbon::createFromTime($hora1, $min1, 0);
        $horaFin = Carbon::createFromTime($hora2, $min2, 0);
        $horaIngreso = Carbon::parse($ingreso);

        return $horaIngreso->between($horaInicio, $horaFin);
    }

    public function morning(DateTimeInterface|WeekDay|Month|string|int|float|null $ingreso, $jornada): bool
    {
        $horaInicio = Carbon::createFromTime(06, 00, 0);
        $horaFin = Carbon::createFromTime(13, 10, 0);
        $morning = 'Mañana';
        $horaIngreso = Carbon::parse($ingreso);

        return $horaIngreso->between($horaInicio, $horaFin) && $jornada === $morning;
    }

    public function afternoon(DateTimeInterface|WeekDay|Month|string|int|float|null $ingreso, $jornada): bool
    {
        $horaInicio = Carbon::createFromTime(13, 00, 0);
        $horaFin = Carbon::createFromTime(18, 10, 0);
        $morning = 'Tarde';
        $horaIngreso = Carbon::parse($ingreso);

        return $horaIngreso->between($horaInicio, $horaFin) && $morning === $jornada;
    }

    public function night(DateTimeInterface|WeekDay|Month|string|int|float|null $ingreso, $jornada): bool
    {
        $horaInicio = Carbon::createFromTime(17, 50, 0);
        $horaFin = Carbon::createFromTime(23, 10, 0);
        $night = 'Noche';
        $horaIngreso = Carbon::parse($ingreso);

        return $horaIngreso->between($horaInicio, $horaFin) && $jornada === $night;
    }
}
