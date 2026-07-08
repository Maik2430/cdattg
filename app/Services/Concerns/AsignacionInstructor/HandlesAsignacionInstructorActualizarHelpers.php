<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use App\Services\InstructorFichaDiasService;

trait HandlesAsignacionInstructorActualizarHelpers
{
    protected function actualizarAsignacionExistente(
        InstructorFichaCaracterizacion $asignacionExistente,
        array $instructorData,
        FichaCaracterizacion $ficha,
        int $fichaId,
        int $userId
    ): InstructorFichaCaracterizacion {
        $asignacionExistente->update([
            'competencia_id' => $instructorData['competencia_id'] ?? null,
            'fecha_inicio' => $instructorData['fecha_inicio'],
            'fecha_fin' => $instructorData['fecha_fin'],
            'user_edit_id' => $userId,
        ]);

        if (isset($instructorData['resultados_aprendizaje']) && is_array($instructorData['resultados_aprendizaje'])) {
            $asignacionExistente->resultadosAprendizaje()->sync($instructorData['resultados_aprendizaje']);
        }

        $this->asignarRolInstructorSiCorresponde(
            $instructorData['instructor_id'],
            $fichaId,
            'actualizar asignación de instructor'
        );

        $asignacionExistente->instructorFichaDias()->delete();

        if (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
            $this->crearDiasHorariosEspecificosAsignacionExistente($asignacionExistente, $instructorData['dias']);

            return $asignacionExistente;
        }

        $diasSeleccionados = $this->extraerDiasSeleccionados($instructorData);

        if (! empty($diasSeleccionados)) {
            $diasParaServicio = $this->crearDiasDesdeFichaAsignacionExistente($asignacionExistente, $ficha, $diasSeleccionados);
            $this->recalcularHorasAsignacionExistente($asignacionExistente, $diasParaServicio);
        }

        return $asignacionExistente;
    }

    protected function crearDiasHorariosEspecificosAsignacionExistente(
        InstructorFichaCaracterizacion $asignacionExistente,
        array $dias
    ): void {
        $diasParaServicio = [];

        foreach ($dias as $diaId => $diaInfo) {
            if (! isset($diaInfo['hora_inicio'], $diaInfo['hora_fin'])) {
                continue;
            }

            $asignacionExistente->instructorFichaDias()->create([
                'dia_id' => $diaId,
                'hora_inicio' => $diaInfo['hora_inicio'],
                'hora_fin' => $diaInfo['hora_fin'],
            ]);

            $diasParaServicio[] = [
                'dia_id' => $diaId,
                'hora_inicio' => $diaInfo['hora_inicio'],
                'hora_fin' => $diaInfo['hora_fin'],
            ];
        }

        $this->recalcularHorasAsignacionExistente($asignacionExistente, $diasParaServicio);
    }

    protected function crearDiasDesdeFichaAsignacionExistente(
        InstructorFichaCaracterizacion $asignacionExistente,
        FichaCaracterizacion $ficha,
        array $diasSeleccionados
    ): array {
        $diasParaServicio = [];

        foreach ($diasSeleccionados as $diaId) {
            $diaFormacionFicha = $ficha->diasFormacion->firstWhere('dia_id', $diaId);

            $horaInicio = $diaFormacionFicha->hora_inicio ?? '08:00';
            $horaFin = $diaFormacionFicha->hora_fin ?? '12:00';

            $asignacionExistente->instructorFichaDias()->create([
                'dia_id' => $diaId,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
            ]);

            $diasParaServicio[] = [
                'dia_id' => $diaId,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
            ];
        }

        return $diasParaServicio;
    }

    protected function recalcularHorasAsignacionExistente(
        InstructorFichaCaracterizacion $asignacionExistente,
        array $diasParaServicio
    ): void {
        if (empty($diasParaServicio)) {
            return;
        }

        $diasService = app(InstructorFichaDiasService::class);
        $diasService->generarFechasEfectivas($asignacionExistente, $diasParaServicio);
        $asignacionExistente->total_horas_instructor = $this->calcularHorasDesdeFechasEfectivas($asignacionExistente, $diasParaServicio);
        $asignacionExistente->save();
    }
}
