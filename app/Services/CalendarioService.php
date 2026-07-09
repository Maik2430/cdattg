<?php

namespace App\Services;

use App\Repositories\FichaDiasFormacionRepository;
use App\Repositories\FichaRepository;
use App\Repositories\InstructorFichaRepository;
use Carbon\Carbon;

class CalendarioService
{
    protected FichaRepository $fichaRepo;

    protected InstructorFichaRepository $instructorFichaRepo;

    protected FichaDiasFormacionRepository $fichaDiasRepo;

    public function __construct(
        FichaRepository $fichaRepo,
        InstructorFichaRepository $instructorFichaRepo,
        FichaDiasFormacionRepository $fichaDiasRepo
    ) {
        $this->fichaRepo = $fichaRepo;
        $this->instructorFichaRepo = $instructorFichaRepo;
        $this->fichaDiasRepo = $fichaDiasRepo;
    }

    /**
     * Genera eventos de calendario para un instructor
     */
    public function generarEventosInstructor(int $instructorId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $fechaFin ?? now()->endOfMonth()->format('Y-m-d');

        $asignaciones = $this->instructorFichaRepo->obtenerPorInstructor($instructorId, true);

        $eventos = [];

        foreach ($asignaciones as $asignacion) {
            $ficha = $asignacion->ficha;
            $diasFormacion = $this->fichaDiasRepo->obtenerPorFicha($ficha->id);

            foreach ($diasFormacion as $diaFormacion) {
                $eventosGenerados = $this->generarEventosPorDia(
                    $ficha,
                    $diaFormacion,
                    $fechaInicio,
                    $fechaFin
                );

                $eventos = array_merge($eventos, $eventosGenerados);
            }
        }

        return $eventos;
    }

    /**
     * Genera eventos para un día específico de formación
     *
     * @param  object  $ficha
     * @param  object  $diaFormacion
     */
    protected function generarEventosPorDia($ficha, $diaFormacion, string $fechaInicio, string $fechaFin): array
    {
        $eventos = [];

        $fechaInicioFicha = Carbon::parse($ficha->fecha_inicio);
        $fechaFinFicha = Carbon::parse($ficha->fecha_fin);

        $fechaActual = Carbon::parse($fechaInicio)->max($fechaInicioFicha);
        $fechaLimite = Carbon::parse($fechaFin)->min($fechaFinFicha);

        while ($fechaActual->lte($fechaLimite)) {
            if ($this->esDiaFormacion($fechaActual, $diaFormacion->diaFormacion->dia_nombre ?? '')) {
                $eventos[] = [
                    'title' => $ficha->programaFormacion->nombre ?? 'Sin programa',
                    'start' => $fechaActual->format('Y-m-d').'T'.$diaFormacion->hora_inicio,
                    'end' => $fechaActual->format('Y-m-d').'T'.$diaFormacion->hora_fin,
                    'backgroundColor' => '#3498db',
                    'ficha_id' => $ficha->id,
                    'ambiente' => $ficha->ambiente->nombre ?? 'Sin ambiente',
                ];
            }
            $fechaActual->addDay();
        }

        return $eventos;
    }

    /**
     * Verifica si una fecha corresponde a un día de formación
     */
    protected function esDiaFormacion(Carbon $fecha, string $diaNombre): bool
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
     * Obtiene conflictos de horario
     */
    public function obtenerConflictosHorario(int $instructorId, string $fecha): array
    {
        $eventos = $this->generarEventosInstructor($instructorId, $fecha, $fecha);

        $conflictos = [];

        for ($i = 0; $i < count($eventos); $i++) {
            for ($j = $i + 1; $j < count($eventos); $j++) {
                if ($this->hayConflicto($eventos[$i], $eventos[$j])) {
                    $conflictos[] = [
                        'evento1' => $eventos[$i],
                        'evento2' => $eventos[$j],
                    ];
                }
            }
        }

        return $conflictos;
    }

    /**
     * Verifica si hay conflicto entre dos eventos
     */
    protected function hayConflicto(array $evento1, array $evento2): bool
    {
        $inicio1 = Carbon::parse($evento1['start']);
        $fin1 = Carbon::parse($evento1['end']);
        $inicio2 = Carbon::parse($evento2['start']);
        $fin2 = Carbon::parse($evento2['end']);

        return $inicio1->lt($fin2) && $fin1->gt($inicio2);
    }
}
