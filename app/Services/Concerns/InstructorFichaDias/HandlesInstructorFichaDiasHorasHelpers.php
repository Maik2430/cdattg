<?php

namespace App\Services\Concerns\InstructorFichaDias;

use App\Models\Competencia;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\ResultadosAprendizaje;
use Carbon\Carbon;

trait HandlesInstructorFichaDiasHorasHelpers
{
    /**
     * Validar coherencia de horas con competencia y resultados de aprendizaje
     */
    protected function validarCoherenciaHorasCompetencia(InstructorFichaCaracterizacion $instructorFicha, array $diasData): array
    {
        $competenciaId = $instructorFicha->competencia_id;
        $resultadosIds = $instructorFicha->resultadosAprendizaje->pluck('id')->toArray();

        if (! $competenciaId && empty($resultadosIds)) {
            return ['valido' => true];
        }

        $fechasEfectivas = $this->generarFechasEfectivas($instructorFicha, $diasData);
        $horasTrabajadas = 0;

        foreach ($fechasEfectivas as $fecha) {
            if ($fecha['hora_inicio'] && $fecha['hora_fin']) {
                $horas = $this->convertirTiempoAHoras($fecha['hora_inicio'], $fecha['hora_fin']);
                $horasTrabajadas += $horas;
            }
        }

        $duracionEsperada = 0;

        if (! empty($resultadosIds)) {
            $resultados = ResultadosAprendizaje::whereIn('id', $resultadosIds)->get();
            $duracionEsperada = $resultados->sum('duracion');
        } elseif ($competenciaId) {
            $competencia = Competencia::find($competenciaId);
            if ($competencia) {
                $duracionEsperada = $competencia->duracion;
            }
        }

        if ($duracionEsperada <= 0) {
            return ['valido' => true];
        }

        $diferencia = abs($horasTrabajadas - $duracionEsperada);
        $porcentajeDiferencia = ($diferencia / $duracionEsperada) * 100;

        if ($porcentajeDiferencia > 10) {
            $competenciaNombre = $competenciaId
                ? (Competencia::find($competenciaId)->nombre ?? 'Competencia')
                : 'Resultados de aprendizaje';

            $mensaje = "⚠️ INCOHERENCIA DE HORAS: Las horas trabajadas ({$horasTrabajadas}h) no son coherentes con la duración esperada ({$duracionEsperada}h) de {$competenciaNombre}. Diferencia: {$diferencia}h ({$porcentajeDiferencia}%). Ajuste las fechas, días u horarios para que coincidan.";

            return [
                'valido' => false,
                'mensaje' => $mensaje,
            ];
        }

        return ['valido' => true];
    }

    /**
     * Convertir tiempo de inicio y fin a horas decimales
     */
    protected function convertirTiempoAHoras(?string $horaInicio, ?string $horaFin): float
    {
        if (! $horaInicio || ! $horaFin) {
            return 0;
        }

        try {
            $inicio = Carbon::parse($horaInicio);
            $fin = Carbon::parse($horaFin);

            if ($fin->lt($inicio)) {
                $fin->addDay();
            }

            $diferencia = $inicio->diffInMinutes($fin);

            return $diferencia / 60;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calcula las horas totales basándose en las fechas efectivas y sus horarios.
     */
    protected function calcularHorasTotalesDesdeFechasEfectivas(array $fechasEfectivas): float
    {
        $horasTotales = 0;

        foreach ($fechasEfectivas as $fecha) {
            if (isset($fecha['hora_inicio']) && isset($fecha['hora_fin'])) {
                $horas = $this->convertirTiempoAHoras($fecha['hora_inicio'], $fecha['hora_fin']);
                $horasTotales += $horas;
            }
        }

        return round($horasTotales, 2);
    }
}
