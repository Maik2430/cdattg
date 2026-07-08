<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use App\Models\ParametroTema;
use Illuminate\Support\Facades\Log;

trait HandlesAsignacionInstructorDisponiblesValidationHelpers
{
    protected function evaluarDisponibilidadInstructor(
        Instructor $instructor,
        FichaCaracterizacion $ficha,
        array $datosFicha,
        int $fichaId
    ): array {
        $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha, $fichaId);
        $validacionSENA = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);

        $evaluacionJornada = $this->evaluarJornadaInstructor($instructor, $ficha, $datosFicha);
        $evaluacionModalidad = $this->evaluarModalidadInstructor($instructor, $ficha, $datosFicha, $fichaId);

        $esDisponible = $disponibilidad['disponible']
            && $validacionSENA['valido']
            && $evaluacionJornada['valido']
            && $evaluacionModalidad['valido'];

        if ($instructor->id == $ficha->instructor_id) {
            $esDisponible = true;
            Log::info('🔍 INSTRUCTOR LÍDER FORZADO COMO DISPONIBLE', [
                'instructor_id' => $instructor->id,
                'disponibilidad_original' => $disponibilidad,
                'validacion_sena_original' => $validacionSENA,
                'es_disponible_original' => $disponibilidad['disponible'] && $validacionSENA['valido'],
                'es_disponible_forzado' => true,
            ]);
        }

        $razonesNoDisponible = array_merge($disponibilidad['razones'], $validacionSENA['errores']);
        if ($evaluacionJornada['razon']) {
            $razonesNoDisponible[] = $evaluacionJornada['razon'];
        }
        if ($evaluacionModalidad['razon']) {
            $razonesNoDisponible[] = $evaluacionModalidad['razon'];
        }

        $mensajeNoDisponible = null;
        if (! $esDisponible && ! empty($razonesNoDisponible)) {
            $razonesPrincipales = array_slice($razonesNoDisponible, 0, 2);
            $mensajeNoDisponible = implode('; ', $razonesPrincipales);
            if (count($razonesNoDisponible) > 2) {
                $mensajeNoDisponible .= '...';
            }
        }

        return [
            'instructor' => $instructor,
            'disponible' => $esDisponible,
            'habilitado' => $esDisponible,
            'razones_no_disponible' => $razonesNoDisponible,
            'mensaje_no_disponible' => $mensajeNoDisponible,
            'conflictos' => $disponibilidad['conflictos'] ?? [],
            'advertencias' => $validacionSENA['advertencias'] ?? [],
        ];
    }

    protected function evaluarJornadaInstructor(
        Instructor $instructor,
        FichaCaracterizacion $ficha,
        array $datosFicha
    ): array {
        $fichaJornadaId = $datosFicha['jornada_id'];
        $instructorLiderId = $datosFicha['instructor_lider_id'];

        if (! $fichaJornadaId || $instructor->id == $instructorLiderId) {
            return ['valido' => true, 'razon' => null];
        }

        $tieneJornadaRelacion = $instructor->jornadas()->where('parametros_temas.id', $fichaJornadaId)->exists();
        $tieneJornadaJSON = in_array($fichaJornadaId, $instructor->jornadas ?? []);
        $tieneJornada = $tieneJornadaRelacion || $tieneJornadaJSON;

        if ($tieneJornada) {
            return ['valido' => true, 'razon' => null];
        }

        $jornadaNombre = $ficha->jornadaFormacion->parametro->name ?? "Jornada ID: {$fichaJornadaId}";

        return [
            'valido' => false,
            'razon' => "No tiene la jornada requerida: {$jornadaNombre}",
        ];
    }

    protected function evaluarModalidadInstructor(
        Instructor $instructor,
        FichaCaracterizacion $ficha,
        array $datosFicha,
        int $fichaId
    ): array {
        $fichaModalidadId = $datosFicha['modalidad_id'];
        $instructorLiderId = $datosFicha['instructor_lider_id'];

        if (! $fichaModalidadId || $instructor->id == $instructorLiderId) {
            return ['valido' => true, 'razon' => null];
        }

        $modalidadParametroTema = ParametroTema::where('parametro_id', $fichaModalidadId)
            ->whereHas('tema', function ($q) {
                $q->where('id', 5);
            })
            ->first();

        if (! $modalidadParametroTema) {
            Log::warning('No se encontró parametro_tema para la modalidad de la ficha', [
                'ficha_id' => $fichaId,
                'modalidad_formacion_id' => $fichaModalidadId,
                'instructor_id' => $instructor->id,
            ]);

            return ['valido' => true, 'razon' => null];
        }

        $modalidadParametroTemaId = $modalidadParametroTema->id;
        $tieneModalidadRelacion = $instructor->modalidades()
            ->where('parametros_temas.id', $modalidadParametroTemaId)
            ->exists();

        $habilidadesPedagogicas = $instructor->habilidades_pedagogicas ?? [];
        $tieneModalidadJSON = is_array($habilidadesPedagogicas)
            && ! empty($habilidadesPedagogicas)
            && in_array($modalidadParametroTemaId, array_map('intval', $habilidadesPedagogicas));

        $tieneModalidad = $tieneModalidadRelacion || $tieneModalidadJSON;

        if ($tieneModalidad) {
            return ['valido' => true, 'razon' => null];
        }

        $modalidadNombre = $ficha->modalidadFormacion->name ?? "Modalidad ID: {$fichaModalidadId}";

        return [
            'valido' => false,
            'razon' => "No tiene la modalidad requerida: {$modalidadNombre}",
        ];
    }
}
