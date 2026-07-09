<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\InstructorFichaCaracterizacion;
use Carbon\Carbon;

trait HandlesInstructorBusinessRulesSuperposicionHelpers
{
    /**
     * Verificar superposición de fechas con fichas existentes considerando jornada y días de la semana
     *
     * @param  array  $diasFormacion  Días de formación de la nueva asignación (opcional)
     * @param  int|null  $fichaIdActual  ID de la ficha actual (para excluir si es instructor principal)
     */
    public function verificarSuperposicionFechas(
        Instructor $instructor,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        ?int $jornadaId = null,
        array $diasFormacion = [],
        ?int $fichaIdActual = null
    ): array {
        $conflictos = [];

        $fichasExistentes = $this->obtenerFichasActivasConDetalles($instructor);

        $diasNuevos = $this->extraerDiasNuevos($diasFormacion);

        foreach ($fichasExistentes as $instructorFicha) {
            if ($this->esMismaFichaPrincipal($fichaIdActual, $instructorFicha, $instructor)) {
                continue;
            }

            $ficha = $instructorFicha->ficha;

            if (! $this->hayConflictoFechas($fechaInicio, $fechaFin, $ficha)) {
                continue;
            }

            if ($this->esConflictoJornada($jornadaId, $ficha)) {
                continue;
            }

            $conflictosFicha = $this->resolverConflictoPorDias(
                $diasNuevos,
                $instructorFicha,
                $ficha
            );

            if (empty($diasNuevos) && empty($conflictosFicha)) {
                $conflictosFicha[] = $this->crearConflictoSinDias($ficha);
            }

            $conflictos = array_merge($conflictos, $conflictosFicha);
        }

        return $conflictos;
    }

    /**
     * Verificar si dos rangos de fechas se superponen
     */
    protected function haySuperposicion(Carbon $inicio1, Carbon $fin1, Carbon $inicio2, Carbon $fin2): bool
    {
        return $inicio1->lte($fin2) && $fin1->gte($inicio2);
    }

    private function obtenerFichasActivasConDetalles(Instructor $instructor)
    {
        return $instructor->instructorFichas()
            ->with(['ficha.jornadaFormacion.parametro', 'instructorFichaDias.dia'])
            ->whereHas('ficha', function ($q) {
                $q->where('status', true);
            })
            ->get();
    }

    private function extraerDiasNuevos(array $diasFormacion): array
    {
        return collect($diasFormacion)->pluck('dia_id')->filter()->toArray();
    }

    private function esMismaFichaPrincipal(
        ?int $fichaIdActual,
        InstructorFichaCaracterizacion $instructorFicha,
        Instructor $instructor
    ): bool {
        if (! $fichaIdActual) {
            return false;
        }

        $ficha = $instructorFicha->ficha;

        return $ficha->id === $fichaIdActual && $ficha->instructor_id === $instructor->id;
    }

    private function hayConflictoFechas(Carbon $fechaInicio, Carbon $fechaFin, FichaCaracterizacion $ficha): bool
    {
        $fechaInicioExistente = Carbon::parse($ficha->fecha_inicio);
        $fechaFinExistente = Carbon::parse($ficha->fecha_fin);

        return $this->haySuperposicion($fechaInicio, $fechaFin, $fechaInicioExistente, $fechaFinExistente);
    }

    private function esConflictoJornada(?int $jornadaNuevaId, FichaCaracterizacion $ficha): bool
    {
        return $jornadaNuevaId && $ficha->jornada_id && $jornadaNuevaId !== $ficha->jornada_id;
    }

    private function resolverConflictoPorDias(
        array $diasNuevos,
        InstructorFichaCaracterizacion $instructorFicha,
        FichaCaracterizacion $ficha
    ): array {
        if (empty($diasNuevos)) {
            return [];
        }

        $diasExistentes = $instructorFicha->instructorFichaDias->pluck('dia_id')->toArray();
        $diasEnComun = array_intersect($diasNuevos, $diasExistentes);

        if (empty($diasEnComun)) {
            return [];
        }

        $diasNombres = $this->obtenerNombresDias($instructorFicha, $diasEnComun);

        return [
            $this->crearConflicto(
                $ficha,
                [
                    'dias_conflicto' => $diasNombres,
                ]
            ),
        ];
    }

    private function obtenerNombresDias(InstructorFichaCaracterizacion $instructorFicha, array $diasEnComun): string
    {
        return $instructorFicha->instructorFichaDias
            ->whereIn('dia_id', $diasEnComun)
            ->pluck('dia.name')
            ->filter()
            ->implode(', ');
    }

    private function crearConflictoSinDias(FichaCaracterizacion $ficha): array
    {
        return $this->crearConflicto($ficha);
    }

    private function crearConflicto(FichaCaracterizacion $ficha, array $extra = []): array
    {
        $conflicto = [
            'ficha_id' => $ficha->id,
            'ficha_numero' => $ficha->ficha,
            'fecha_inicio' => $ficha->fecha_inicio,
            'fecha_fin' => $ficha->fecha_fin,
            'programa' => $ficha->programaFormacion->nombre ?? self::TEXTO_SIN_PROGRAMA,
            'jornada' => $ficha->jornadaFormacion->parametro->name ?? 'Sin jornada',
        ];

        return array_merge($conflicto, $extra);
    }
}
