<?php

namespace App\Services\Concerns\InstructorBusinessRules;

use App\Models\Instructor;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorBusinessRulesReglasSenaActions
{
    /**
     * Validar reglas específicas del SENA para asignación de fichas
     */
    public function validarReglasSENA(Instructor $instructor, array $datosFicha): array
    {
        $resultado = $this->inicializarResultadoReglasSena();

        try {
            $this->evaluarExperienciaMinima($instructor, $resultado);
            $this->evaluarRegionalAsignacion($instructor, $datosFicha, $resultado);
            $this->evaluarDisponibilidadGeneral($instructor, $datosFicha, $resultado);
            $this->agregarAdvertenciasGenerales($instructor, $resultado);
        } catch (\Exception $e) {
            Log::error('Error validando reglas SENA', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'datos_ficha' => $datosFicha,
            ]);

            $resultado['valido'] = false;
            $resultado['errores'][] = 'Error interno al validar reglas de negocio';
        }

        return $resultado;
    }

    private function inicializarResultadoReglasSena(): array
    {
        return [
            'valido' => true,
            'errores' => [],
            'advertencias' => [],
        ];
    }

    private function evaluarExperienciaMinima(Instructor $instructor, array &$resultado): void
    {
        if ($this->validarExperienciaMinima($instructor)) {
            return;
        }

        $resultado['valido'] = false;
        $experienciaActual = $instructor->anos_experiencia ?? 0;
        $resultado['errores'][] =
            "El instructor no cumple con la experiencia mínima requerida ({$experienciaActual}/"
            .self::EXPERIENCIA_MINIMA
            .' años). Ejemplo: Necesita al menos '
            .self::EXPERIENCIA_MINIMA
            .' año de experiencia';
    }

    private function evaluarRegionalAsignacion(Instructor $instructor, array $datosFicha, array &$resultado): void
    {
        if (! isset($datosFicha['regional_id']) || $instructor->regional_id == $datosFicha['regional_id']) {
            return;
        }

        $resultado['valido'] = false;
        $regionalInstructor = $instructor->regional->nombre ?? 'Sin regional';
        $regionalFicha = $datosFicha['regional_nombre'] ?? 'Sin especificar';
        $resultado['errores'][] =
            'El instructor debe pertenecer a la misma regional que la ficha. Instructor: '
            ."{$regionalInstructor}, Ficha: {$regionalFicha}";
    }

    private function evaluarDisponibilidadGeneral(Instructor $instructor, array $datosFicha, array &$resultado): void
    {
        if (! $resultado['valido']) {
            return;
        }

        $disponibilidad = $this->verificarDisponibilidad($instructor, $datosFicha);
        if ($disponibilidad['disponible']) {
            return;
        }

        $resultado['valido'] = false;
        $resultado['errores'] = array_merge($resultado['errores'], $disponibilidad['razones']);
    }

    private function agregarAdvertenciasGenerales(Instructor $instructor, array &$resultado): void
    {
        if ($this->contarTotalFichasAsignadas($instructor) >= 4) {
            $resultado['advertencias'][] = 'El instructor ya tiene 4 fichas asignadas, considere la carga de trabajo';
        }

        if (($instructor->anos_experiencia ?? 0) < 3) {
            $resultado['advertencias'][] = 'Instructor con poca experiencia, considere asignar fichas básicas';
        }
    }
}
