<?php

namespace App\Services\Aitg;

use App\Models\Aitg\PlanContratacion;

/** Construye configuración JSON para el formulario dinámico AITG. */
class AitgPlanFormConfigBuilder
{
    public function build(?PlanContratacion $plan = null): array
    {
        $perfiles = old('perfiles');
        if ($perfiles === null && $plan) {
            $perfiles = $plan->perfiles->map(fn ($pf) => [
                'id' => $pf->id,
                'descripcion_criterio' => $pf->descripcion_criterio,
                'descripcion_criterio_programa' => $pf->descripcion_criterio_programa,
                'incluye_experiencia' => $pf->incluye_experiencia,
                'experiencia_relacionada_meses' => $pf->experiencia_relacionada_meses ?? 0,
                'experiencia_docencia_meses' => $pf->experiencia_docencia_meses ?? 0,
            ])->values()->all();
        }

        $puntos = old('puntos_adicionales');
        if ($puntos === null && $plan) {
            $puntos = $plan->puntosAdicionales->map(fn ($pt) => [
                'id' => $pt->id,
                'descripcion' => $pt->descripcion,
                'puntaje_adicional' => $pt->puntaje_adicional,
            ])->values()->all();
        }

        $checklist = old('checklist');
        if ($checklist === null && $plan) {
            $checklist = $plan->checklist->map(fn ($item) => [
                'id' => $item->id,
                'descripcion_criterio' => $item->descripcion_criterio,
            ])->values()->all();
        }

        return [
            'tipoRegistro' => old('tipo_registro_perfil', $plan?->tipo_registro_perfil ?? 'directo'),
            'perfiles' => $perfiles ?? [],
            'checklist' => $checklist ?? [],
            'puntos' => $puntos ?? [],
        ];
    }
}
