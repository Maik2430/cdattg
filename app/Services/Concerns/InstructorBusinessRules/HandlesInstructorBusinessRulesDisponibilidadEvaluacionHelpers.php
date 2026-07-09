<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\Instructor;
use Carbon\Carbon;

trait HandlesInstructorBusinessRulesDisponibilidadEvaluacionHelpers
{
    /**
     * Verificar si el instructor excede el límite de fichas activas
     */
    public function excedeLimiteFichasActivas(Instructor $instructor): bool
    {
        return $this->contarFichasActivas($instructor) >= self::MAX_FICHAS_ACTIVAS;
    }

    private function evaluarLimiteFichasActivas(Instructor $instructor, array &$resultado): void
    {
        if (! $this->excedeLimiteFichasActivas($instructor)) {
            return;
        }

        $resultado['disponible'] = false;

        $fichasActivas = $this->obtenerFichasActivas($instructor);
        $ejemploFichas = $fichasActivas->take(2)->map(function ($ficha) {
            return "Ficha {$ficha->ficha} ({$ficha->programaFormacion->nombre})";
        })->implode(', ');

        $resultado['razones'][] =
            'El instructor excede el límite máximo de fichas activas ('
            .count($fichasActivas)
            .'/'
            .self::MAX_FICHAS_ACTIVAS
            ."). Ejemplo: {$ejemploFichas}";
    }

    private function evaluarConflictosAsignacion(
        Instructor $instructor,
        Carbon $fechaInicio,
        Carbon $fechaFin,
        array $datosFicha,
        ?int $fichaIdActual,
        array &$resultado
    ): void {
        $jornadaId = $datosFicha['jornada_id'] ?? null;
        $diasFormacion = $datosFicha['dias_formacion'] ?? [];
        $conflictos = $this->verificarSuperposicionFechas(
            $instructor,
            $fechaInicio,
            $fechaFin,
            $jornadaId,
            $diasFormacion,
            $fichaIdActual
        );

        if (empty($conflictos)) {
            return;
        }

        $resultado['disponible'] = false;
        $resultado['conflictos'] = $conflictos;
        $resultado['razones'][] = $this->construirMensajeConflicto($conflictos[0]);
    }

    private function construirMensajeConflicto(array $conflicto): string
    {
        $programaNombre = $conflicto['programa'] ?? self::TEXTO_SIN_PROGRAMA;
        $jornadaInfo = isset($conflicto['jornada']) ? " (Jornada: {$conflicto['jornada']})" : '';
        $diasTexto = $conflicto['dias_conflicto'] ?? '';
        $diasInfo = $diasTexto !== '' ? " en los días: {$diasTexto}" : '';
        $fechaInicio = Carbon::parse($conflicto['fecha_inicio'])->format('d/m/Y');
        $fechaFin = Carbon::parse($conflicto['fecha_fin'])->format('d/m/Y');

        return "El instructor tiene fichas con fechas superpuestas en la misma jornada{$diasInfo}. "
            ."Ejemplo: Ficha {$conflicto['ficha_numero']} ({$programaNombre}){$jornadaInfo} del "
            ."{$fechaInicio} al {$fechaFin}";
    }

    /**
     * Obtener fichas activas del instructor
     */
    private function obtenerFichasActivas(Instructor $instructor)
    {
        return $instructor->instructorFichas()
            ->with(['ficha.programaFormacion'])
            ->whereHas('ficha', function ($q) {
                $q->where('status', true)
                    ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get()
            ->pluck('ficha');
    }
}
