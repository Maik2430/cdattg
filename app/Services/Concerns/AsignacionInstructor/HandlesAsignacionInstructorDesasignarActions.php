<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\AsignacionInstructorLog;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\InstructorFichaCaracterizacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAsignacionInstructorDesasignarActions
{
    public function desasignarInstructor(int $instructorId, int $fichaId, int $userId): array
    {
        DB::beginTransaction();

        try {
            $asignacion = InstructorFichaCaracterizacion::where('instructor_id', $instructorId)
                ->where('ficha_id', $fichaId)
                ->with('instructorFichaDias.dia')
                ->firstOrFail();

            $instructor = Instructor::find($instructorId);
            $ficha = FichaCaracterizacion::find($fichaId);

            $datosAnteriores = $this->prepararDatosAnterioresDesasignacion($asignacion);

            DB::table('asistencia_aprendices')
                ->where('instructor_ficha_id', $asignacion->id)
                ->update(['instructor_ficha_id' => null]);

            $asignacion->instructorFichaDias()->delete();
            $asignacionId = $asignacion->id;
            $asignacion->delete();

            $this->removerRolInstructorSiSinAsignaciones($instructorId, $fichaId, $asignacionId);

            DB::commit();

            AsignacionInstructorLog::crearLog(
                $instructorId,
                $fichaId,
                'desasignar',
                'exitoso',
                "Instructor {$instructor->nombre_completo} desasignado exitosamente de la ficha {$ficha->ficha}. Las asistencias registradas se mantienen pero se desvinculan de la asignación específica.",
                $userId,
                ['motivo' => 'desasignacion_manual'],
                $datosAnteriores
            );

            Log::info('Instructor desasignado exitosamente', [
                'instructor_id' => $instructorId,
                'ficha_id' => $fichaId,
                'user_id' => $userId,
            ]);

            return [
                'success' => true,
                'message' => 'Instructor desasignado exitosamente. Las asistencias registradas se mantienen pero se desvinculan de la asignación específica.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            AsignacionInstructorLog::crearLog(
                $instructorId,
                $fichaId,
                'desasignar',
                'error',
                'Error al desasignar instructor: '.$e->getMessage(),
                $userId,
                ['error' => $e->getMessage()]
            );

            Log::error('Error desasignando instructor', [
                'instructor_id' => $instructorId,
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function prepararDatosAnterioresDesasignacion(InstructorFichaCaracterizacion $asignacion): array
    {
        $diasFormacion = $asignacion->instructorFichaDias->map(function ($dia) {
            return [
                'dia_id' => $dia->dia_id,
                'dia_nombre' => $dia->dia->name ?? 'Sin nombre',
            ];
        })->toArray();

        return [
            'fecha_inicio' => $asignacion->fecha_inicio,
            'fecha_fin' => $asignacion->fecha_fin,
            'total_horas' => $asignacion->total_horas_instructor,
            'dias_formacion' => $diasFormacion,
        ];
    }
}
