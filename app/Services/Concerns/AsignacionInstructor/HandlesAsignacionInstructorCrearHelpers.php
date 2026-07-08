<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\FichaCaracterizacion;
use App\Models\InstructorFichaCaracterizacion;
use App\Services\InstructorFichaDiasService;

trait HandlesAsignacionInstructorCrearHelpers
{
    protected function crearAsignacion(array $instructorData, int $fichaId, int $userId): InstructorFichaCaracterizacion
    {
        $ficha = FichaCaracterizacion::with(['jornadaFormacion.parametro', 'diasFormacion'])->findOrFail($fichaId);

        $instructorFicha = InstructorFichaCaracterizacion::create([
            'instructor_id' => $instructorData['instructor_id'],
            'ficha_id' => $fichaId,
            'competencia_id' => $instructorData['competencia_id'] ?? null,
            'fecha_inicio' => $instructorData['fecha_inicio'],
            'fecha_fin' => $instructorData['fecha_fin'],
            'total_horas_instructor' => 0,
        ]);

        if (isset($instructorData['resultados_aprendizaje']) && is_array($instructorData['resultados_aprendizaje'])) {
            $instructorFicha->resultadosAprendizaje()->sync($instructorData['resultados_aprendizaje']);
        }

        if (isset($instructorData['dias']) && is_array($instructorData['dias'])) {
            $this->crearDiasHorariosEspecificosNuevaAsignacion($instructorFicha, $instructorData['dias']);
            $this->asignarRolInstructorSiCorresponde($instructorData['instructor_id'], $fichaId, 'asignar instructor a ficha');

            return $instructorFicha;
        }

        $diasSeleccionados = $this->extraerDiasSeleccionados($instructorData);

        if (! empty($diasSeleccionados)) {
            $diasParaServicio = $this->crearDiasDesdeFichaNuevaAsignacion($instructorFicha, $ficha, $diasSeleccionados);

            if (! empty($diasParaServicio)) {
                $diasService = app(InstructorFichaDiasService::class);
                $diasService->generarFechasEfectivas($instructorFicha, $diasParaServicio);
                $instructorFicha->total_horas_instructor = $this->calcularHorasDesdeFechasEfectivas($instructorFicha, $diasParaServicio);
                $instructorFicha->save();
            }
        }

        $this->asignarRolInstructorSiCorresponde($instructorData['instructor_id'], $fichaId, 'asignar instructor a ficha');

        return $instructorFicha;
    }

    protected function crearDiasHorariosEspecificosNuevaAsignacion(
        InstructorFichaCaracterizacion $instructorFicha,
        array $dias
    ): void {
        $diasParaServicio = [];

        foreach ($dias as $diaId => $diaInfo) {
            if (! isset($diaInfo['hora_inicio'], $diaInfo['hora_fin'])) {
                continue;
            }

            $instructorFicha->instructorFichaDias()->create([
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

        if (! empty($diasParaServicio)) {
            $instructorFicha->total_horas_instructor = $this->calcularHorasDesdeFechasEfectivas($instructorFicha, $diasParaServicio);
            $instructorFicha->save();
        }
    }

    protected function crearDiasDesdeFichaNuevaAsignacion(
        InstructorFichaCaracterizacion $instructorFicha,
        FichaCaracterizacion $ficha,
        array $diasSeleccionados
    ): array {
        $diasParaServicio = [];

        foreach ($diasSeleccionados as $diaId) {
            $diaFormacionFicha = $ficha->diasFormacion->firstWhere('dia_id', $diaId);

            $horaInicio = $diaFormacionFicha->hora_inicio ?? ($ficha->jornadaFormacion->hora_inicio ?? '08:00');
            $horaFin = $diaFormacionFicha->hora_fin ?? ($ficha->jornadaFormacion->hora_fin ?? '12:00');

            $instructorFicha->instructorFichaDias()->create([
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
}
