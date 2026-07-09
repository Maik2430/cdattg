<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\Instructor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorBusinessRulesDisponibilidadActions
{
    /**
     * Verificar disponibilidad del instructor para una nueva ficha
     */
    public function verificarDisponibilidad(
        Instructor $instructor,
        array $datosFicha,
        ?int $fichaIdActual = null
    ): array {
        $resultado = $this->inicializarResultadoDisponibilidad();

        try {
            if (! $this->verificarEstadoActivo($instructor, $resultado)) {
                return $resultado;
            }

            $fechaInicio = Carbon::parse($datosFicha['fecha_inicio']);
            $fechaFin = Carbon::parse($datosFicha['fecha_fin']);

            $this->evaluarLimiteFichasActivas($instructor, $resultado);
            $this->evaluarConflictosAsignacion(
                $instructor,
                $fechaInicio,
                $fechaFin,
                $datosFicha,
                $fichaIdActual,
                $resultado
            );

            // VALIDACIÓN DESHABILITADA: Verificar carga horaria semanal
            // Esta validación estaba comparando incorrectamente horas totales vs límite semanal
            // $cargaHoraria = $this->calcularCargaHorariaSemanal(
            //     $instructor,
            //     $fechaInicio,
            //     $fechaFin,
            //     $datosFicha['horas_semanales'] ?? 0,
            //     $jornadaId
            // );
            // if ($cargaHoraria > self::MAX_HORAS_SEMANA) {
            //     $resultado['disponible'] = false;
            //     $resultado['razones'][] =
            //         "El instructor excedería la carga horaria máxima semanal en esta jornada "
            //         . "({$cargaHoraria}h > " . self::MAX_HORAS_SEMANA . "h). Ejemplo: Actualmente tiene "
            //         . ($cargaHoraria - ($datosFicha['horas_semanales'] ?? 0))
            //         . "h semanales asignadas en la misma jornada";
            // }

            // Validar especialidad del instructor
            $this->evaluarEspecialidadRequerida($instructor, $datosFicha, $resultado);
        } catch (\Exception $e) {
            Log::error('Error verificando disponibilidad del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'datos_ficha' => $datosFicha,
            ]);

            $resultado['disponible'] = false;
            $resultado['razones'][] = 'Error interno al verificar disponibilidad';
        }

        return $resultado;
    }

    private function inicializarResultadoDisponibilidad(): array
    {
        return [
            'disponible' => true,
            'razones' => [],
            'conflictos' => [],
        ];
    }

    private function verificarEstadoActivo(Instructor $instructor, array &$resultado): bool
    {
        if ($instructor->status) {
            return true;
        }

        $resultado['disponible'] = false;
        $resultado['razones'][] = 'El instructor está inactivo';

        return false;
    }

    /**
     * Evaluar si el instructor tiene la especialidad requerida
     */
    private function evaluarEspecialidadRequerida(Instructor $instructor, array $datosFicha, array &$resultado): void
    {
        $especialidadRequeridaId = $datosFicha['especialidad_requerida_id'] ?? null;
        $instructorLiderId = $datosFicha['instructor_lider_id'] ?? null;

        // Si no hay especialidad requerida, cualquier instructor puede tomar la ficha
        if (! $especialidadRequeridaId) {
            return;
        }

        // El instructor líder siempre pasa la validación de especialidad
        if ($instructorLiderId && $instructor->id == $instructorLiderId) {
            return;
        }

        // Obtener especialidades del instructor
        $especialidades = $instructor->especialidades ?? [];

        if (empty($especialidades)) {
            $resultado['disponible'] = false;
            $especialidadNombre = $datosFicha['especialidad_requerida'] ?? 'especialidad requerida';
            $resultado['razones'][] = "El instructor no tiene especialidades asignadas. La ficha requiere: {$especialidadNombre}";

            return;
        }

        // Verificar si tiene la especialidad requerida (principal o secundaria)
        $especialidadPrincipal = $especialidades['principal'] ?? null;
        $especialidadesSecundarias = $especialidades['secundarias'] ?? [];

        $tieneEspecialidad = false;

        // Verificar si la especialidad requerida coincide con la principal
        if ($especialidadPrincipal == $especialidadRequeridaId) {
            $tieneEspecialidad = true;
        }

        // Verificar si está en las secundarias
        if (! $tieneEspecialidad && is_array($especialidadesSecundarias)) {
            $tieneEspecialidad = in_array($especialidadRequeridaId, $especialidadesSecundarias);
        }

        if (! $tieneEspecialidad) {
            $resultado['disponible'] = false;
            $especialidadNombre = $datosFicha['especialidad_requerida'] ?? 'especialidad requerida';
            $resultado['razones'][] = "El instructor no tiene la especialidad requerida para esta ficha. Requerida: {$especialidadNombre}";
        }
    }
}
