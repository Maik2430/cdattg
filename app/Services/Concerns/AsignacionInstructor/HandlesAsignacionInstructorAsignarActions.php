<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAsignacionInstructorAsignarActions
{
    public function asignarInstructores(array $instructoresData, int $fichaId, int $instructorPrincipalId, int $userId): array
    {
        DB::beginTransaction();

        $instructorIdConError = null;

        try {
            $ficha = FichaCaracterizacion::with([
                'programaFormacion.redConocimiento',
                'diasFormacion',
                'jornadaFormacion.parametro',
            ])->findOrFail($fichaId);

            if (! $ficha->status) {
                throw new \Exception('La ficha no está activa');
            }

            $datosFicha = $this->prepararDatosFichaAsignacion($ficha);
            $instructoresValidos = $this->validarInstructoresParaAsignacion(
                $instructoresData,
                $datosFicha,
                $fichaId,
                $instructorIdConError
            );

            $asignacionesExistentes = $ficha->instructorFicha()->get();
            $asignacionesCreadas = $this->procesarAsignacionesInstructores(
                $instructoresValidos,
                $asignacionesExistentes,
                $ficha,
                $fichaId,
                $userId
            );

            DB::commit();

            $this->registrarLogsAsignacionExitosa($asignacionesCreadas, $fichaId, $instructorPrincipalId, $userId);

            Log::info('Instructores asignados exitosamente', [
                'ficha_id' => $fichaId,
                'instructor_principal_id' => $instructorPrincipalId,
                'total_instructores' => count($asignacionesCreadas),
                'user_id' => $userId,
            ]);

            return [
                'success' => true,
                'message' => 'Instructores asignados exitosamente',
                'asignaciones' => $asignacionesCreadas,
                'total_asignados' => count($asignacionesCreadas),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->registrarErrorAsignacion(
                $e,
                $instructoresData,
                $fichaId,
                $userId,
                $instructorIdConError
            );
        }
    }

    protected function prepararDatosFichaAsignacion(FichaCaracterizacion $ficha): array
    {
        $redConocimientoId = $ficha->programaFormacion->red_conocimiento_id ?? null;

        return [
            'fecha_inicio' => $ficha->fecha_inicio,
            'fecha_fin' => $ficha->fecha_fin,
            'especialidad_requerida' => $ficha->programaFormacion->redConocimiento->nombre ?? null,
            'especialidad_requerida_id' => $redConocimientoId,
            'instructor_lider_id' => $ficha->instructor_id,
            'regional_id' => $ficha->regional_id,
            'jornada_id' => $ficha->jornada_id,
            'horas_semanales' => 0,
        ];
    }

    protected function validarInstructoresParaAsignacion(
        array $instructoresData,
        array $datosFicha,
        int $fichaId,
        ?int &$instructorIdConError
    ): array {
        $instructoresValidos = [];

        foreach ($instructoresData as $instructorData) {
            $instructor = Instructor::findOrFail($instructorData['instructor_id']);
            $instructorIdConError = $instructor->id;

            $datosFicha['horas_semanales'] = 0;
            $datosFicha['dias_formacion'] = $instructorData['dias_semana'] ?? ($instructorData['dias_formacion'] ?? []);

            $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha, $fichaId);

            if (! $disponibilidad['disponible']) {
                throw new \Exception(
                    "El instructor {$instructor->nombre_completo} no está disponible: ".implode(', ', $disponibilidad['razones'])
                );
            }

            $instructoresValidos[] = $instructorData;
            $instructorIdConError = null;
        }

        return $instructoresValidos;
    }

    protected function procesarAsignacionesInstructores(
        array $instructoresValidos,
        Collection $asignacionesExistentes,
        FichaCaracterizacion $ficha,
        int $fichaId,
        int $userId
    ): array {
        $asignacionesCreadas = [];

        foreach ($instructoresValidos as $instructorData) {
            $asignacionExistente = $asignacionesExistentes->firstWhere('instructor_id', $instructorData['instructor_id']);

            if ($asignacionExistente) {
                $asignacionesCreadas[] = $this->actualizarAsignacionExistente(
                    $asignacionExistente,
                    $instructorData,
                    $ficha,
                    $fichaId,
                    $userId
                );
            } else {
                $asignacionesCreadas[] = $this->crearAsignacion($instructorData, $fichaId, $userId);
            }
        }

        return $asignacionesCreadas;
    }
}
