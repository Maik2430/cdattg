<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Carbon\Carbon;

trait HandlesInstructorDashboardCalendarHelpers
{
    /**
     * Obtener eventos del calendario para el instructor
     */
    private function obtenerEventosCalendario(Instructor $instructor): array
    {
        $eventos = [];

        $fichasActivas = $instructor->instructorFichas()
            ->with(['ficha.diasFormacion'])
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get();

        foreach ($fichasActivas as $instructorFicha) {
            $ficha = $instructorFicha->ficha;

            foreach ($ficha->diasFormacion as $diaFormacion) {
                $fechaInicio = Carbon::parse($ficha->fecha_inicio);
                $fechaFin = Carbon::parse($ficha->fecha_fin);

                // Generar eventos para cada día de formación en el rango
                $fechaActual = $fechaInicio->copy();
                while ($fechaActual->lte($fechaFin)) {
                    if ($this->esDiaFormacion($fechaActual, $diaFormacion->dia_nombre)) {
                        $eventos[] = [
                            'title' => $ficha->programaFormacion->nombre ?? 'Sin programa',
                            'start' => $fechaActual->format('Y-m-d').'T'.$diaFormacion->hora_inicio,
                            'end' => $fechaActual->format('Y-m-d').'T'.$diaFormacion->hora_fin,
                            'backgroundColor' => $this->obtenerColorPorEspecialidad(
                                $ficha->programaFormacion->redConocimiento->nombre ?? ''
                            ),
                            'borderColor' => $this->obtenerColorPorEspecialidad(
                                $ficha->programaFormacion->redConocimiento->nombre ?? ''
                            ),
                            'extendedProps' => [
                                'ficha_id' => $ficha->id,
                                'ambiente' => $ficha->ambiente->nombre ?? 'Sin ambiente',
                                'sede' => $ficha->ambiente->sede->nombre ?? 'Sin sede',
                                'modalidad' => $ficha->modalidadFormacion->nombre ?? 'Sin modalidad',
                            ],
                        ];
                    }
                    $fechaActual->addDay();
                }
            }
        }

        return $eventos;
    }

    /**
     * Verificar si una fecha corresponde a un día de formación
     */
    private function esDiaFormacion(Carbon $fecha, string $diaNombre): bool
    {
        $diasSemana = [
            'Lunes' => 1,
            'Martes' => 2,
            'Miércoles' => 3,
            'Jueves' => 4,
            'Viernes' => 5,
            'Sábado' => 6,
            'Domingo' => 0,
        ];

        return $fecha->dayOfWeek === ($diasSemana[$diaNombre] ?? -1);
    }

    /**
     * Obtener color por especialidad para el calendario
     */
    private function obtenerColorPorEspecialidad(string $especialidad): string
    {
        $colores = [
            'Tecnologías de la Información y las Comunicaciones' => '#3498db',
            'Electrónica' => '#e74c3c',
            'Mecánica Industrial' => '#f39c12',
            'Construcción' => '#2ecc71',
            'Gastronomía' => '#9b59b6',
            'Agropecuaria' => '#27ae60',
            'Comercio' => '#34495e',
        ];

        return $colores[$especialidad] ?? '#95a5a6';
    }
}
