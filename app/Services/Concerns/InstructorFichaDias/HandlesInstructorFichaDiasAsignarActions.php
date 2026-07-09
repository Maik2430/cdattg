<?php

namespace App\Services\Concerns\InstructorFichaDias;

use App\Models\InstructorFichaCaracterizacion;
use App\Models\InstructorFichaDias;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorFichaDiasAsignarActions
{
    /**
     * Asigna días de formación a un instructor en una ficha específica.
     *
     * @param  int  $instructorFichaId  ID de la relación instructor-ficha
     * @param  array  $diasData  Array de días con estructura: [['dia_id' => 12, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], ...]
     */
    public function asignarDiasInstructor(int $instructorFichaId, array $diasData): array
    {
        try {
            \Log::info('Iniciando asignación de días', [
                'instructor_ficha_id' => $instructorFichaId,
                'dias_data' => $diasData,
            ]);

            DB::beginTransaction();

            $instructorFicha = InstructorFichaCaracterizacion::with(['instructor', 'ficha', 'competencia', 'resultadosAprendizaje'])->findOrFail($instructorFichaId);

            \Log::info('Instructor-ficha encontrado', ['instructor_ficha' => $instructorFicha]);

            $validacion = $this->validarDisponibilidadInstructor($instructorFicha, $diasData);

            if (! $validacion['disponible']) {
                $conflictos = $validacion['conflictos'];
                $mensaje = 'El instructor tiene conflictos de horario, jornada o fechas con otras fichas asignadas:';

                $detallesConflictos = [];
                foreach ($conflictos as $conflicto) {
                    $detalle = "• {$conflicto['dia_nombre']}: Ficha {$conflicto['ficha_conflicto']} ({$conflicto['programa_conflicto']}) - ";
                    $detalle .= "Jornada: {$conflicto['jornada_conflicto']}, ";
                    $detalle .= "Fechas: {$conflicto['fecha_inicio_conflicto']} a {$conflicto['fecha_fin_conflicto']}, ";
                    $detalle .= "Horario: {$conflicto['horario_conflicto']} (solicitado: {$conflicto['horario_solicitado']})";
                    $detallesConflictos[] = $detalle;
                }

                $mensajeCompleto = $mensaje."\n\n".implode("\n", $detallesConflictos);

                return [
                    'success' => false,
                    'message' => $mensajeCompleto,
                    'conflictos' => $conflictos,
                ];
            }

            $validacionHoras = $this->validarCoherenciaHorasCompetencia($instructorFicha, $diasData);
            if (! $validacionHoras['valido']) {
                return [
                    'success' => false,
                    'message' => $validacionHoras['mensaje'],
                    'advertencia' => true,
                ];
            }

            InstructorFichaDias::where('instructor_ficha_id', $instructorFichaId)->delete();

            $diasCreados = [];
            foreach ($diasData as $diaData) {
                $diaCreado = InstructorFichaDias::create([
                    'instructor_ficha_id' => $instructorFichaId,
                    'dia_id' => $diaData['dia_id'],
                    'hora_inicio' => $diaData['hora_inicio'] ?? null,
                    'hora_fin' => $diaData['hora_fin'] ?? null,
                ]);

                $diasCreados[] = $diaCreado;
            }

            $fechasEfectivas = $this->generarFechasEfectivas($instructorFicha, $diasData);

            $horasTotales = $this->calcularHorasTotalesDesdeFechasEfectivas($fechasEfectivas);
            $instructorFicha->total_horas_instructor = $horasTotales;
            $instructorFicha->save();

            DB::commit();

            Log::info('✓ Días de formación asignados exitosamente', [
                'instructor_ficha_id' => $instructorFichaId,
                'instructor_id' => $instructorFicha->instructor_id,
                'ficha_id' => $instructorFicha->ficha_id,
                'cantidad_dias' => count($diasCreados),
                'cantidad_fechas_efectivas' => count($fechasEfectivas),
            ]);

            return [
                'success' => true,
                'message' => 'Días de formación asignados correctamente',
                'dias_asignados' => $diasCreados,
                'fechas_efectivas' => $fechasEfectivas,
                'total_sesiones' => count($fechasEfectivas),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('✗ Error al asignar días de formación', [
                'instructor_ficha_id' => $instructorFichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al asignar días: '.$e->getMessage(),
            ];
        }
    }
}
