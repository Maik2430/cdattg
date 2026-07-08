<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\AsignacionInstructorLog;
use Illuminate\Support\Facades\Log;

trait HandlesAsignacionInstructorAsignarLogHelpers
{
    protected function registrarLogsAsignacionExitosa(
        array $asignacionesCreadas,
        int $fichaId,
        int $instructorPrincipalId,
        int $userId
    ): void {
        foreach ($asignacionesCreadas as $asignacion) {
            AsignacionInstructorLog::crearLog(
                $asignacion->instructor_id,
                $fichaId,
                'asignar',
                'exitoso',
                'Instructor asignado exitosamente a la ficha',
                $userId,
                [
                    'fecha_inicio' => $asignacion->fecha_inicio,
                    'fecha_fin' => $asignacion->fecha_fin,
                    'total_horas' => $asignacion->total_horas_instructor,
                    'es_principal' => $asignacion->instructor_id == $instructorPrincipalId,
                ],
                null,
                [
                    'fecha_inicio' => $asignacion->fecha_inicio,
                    'fecha_fin' => $asignacion->fecha_fin,
                    'total_horas' => $asignacion->total_horas_instructor,
                ]
            );
        }
    }

    protected function registrarErrorAsignacion(
        \Exception $e,
        array $instructoresData,
        int $fichaId,
        int $userId,
        ?int $instructorIdConError
    ): array {
        AsignacionInstructorLog::crearLog(
            $instructorIdConError,
            $fichaId,
            'asignar',
            'error',
            'Error en asignación de instructores: '.$e->getMessage(),
            $userId,
            [
                'error' => $e->getMessage(),
                'instructores_data' => $instructoresData,
                'instructor_con_error' => $instructorIdConError,
            ]
        );

        Log::error('Error asignando instructores', [
            'ficha_id' => $fichaId,
            'error' => $e->getMessage(),
            'user_id' => $userId,
            'instructor_id_con_error' => $instructorIdConError,
            'instructores_data' => $instructoresData,
        ]);

        return [
            'success' => false,
            'message' => $e->getMessage(),
            'error' => $e->getMessage(),
        ];
    }
}
