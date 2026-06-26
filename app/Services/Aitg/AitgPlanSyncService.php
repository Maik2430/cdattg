<?php

namespace App\Services\Aitg;

use App\Models\Aitg\ChecklistPlan;
use App\Models\Aitg\PerfilPlan;
use App\Models\Aitg\PlanContratacion;
use App\Models\Aitg\PuntoAdicional;

/** Sincroniza perfiles, checklist y puntos adicionales de un plan AITG. */
class AitgPlanSyncService
{
    /** @param array<int, array<string, mixed>> $perfilesData */
    public function syncPerfiles(PlanContratacion $plan, array $perfilesData): void
    {
        $idsMantener = [];

        foreach (array_values($perfilesData) as $index => $row) {
            $descripcion = trim((string) ($row['descripcion_criterio'] ?? ''));
            if ($descripcion === '') {
                continue;
            }

            $incluyeExperiencia = filter_var($row['incluye_experiencia'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $consecutivo = $index + 1;
            $payload = [
                'consecutivo' => $consecutivo,
                'descripcion_criterio' => $descripcion,
                'descripcion_criterio_programa' => $this->descripcionPrograma($plan, $row),
                'incluye_experiencia' => $incluyeExperiencia,
                'experiencia_relacionada_meses' => $incluyeExperiencia
                    ? (int) ($row['experiencia_relacionada_meses'] ?? 0)
                    : null,
                'experiencia_docencia_meses' => $incluyeExperiencia
                    ? (int) ($row['experiencia_docencia_meses'] ?? 0)
                    : null,
                'requiere_documento' => filter_var($row['requiere_documento'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'documento_nombre' => trim((string) ($row['documento_nombre'] ?? '')) ?: null,
                'documento_descripcion' => trim((string) ($row['documento_descripcion'] ?? '')) ?: null,
                'documento_es_obligatorio' => filter_var($row['documento_es_obligatorio'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];

            if (! empty($row['id'])) {
                $perfil = PerfilPlan::where('plan_contratacion_id', $plan->id)
                    ->where('id', $row['id'])
                    ->first();
                if ($perfil) {
                    $perfil->update($payload);
                    $idsMantener[] = $perfil->id;
                    continue;
                }
            }

            $nuevo = $plan->perfiles()->create($payload);
            $idsMantener[] = $nuevo->id;
        }

        $plan->perfiles()->whereNotIn('id', $idsMantener)->delete();
    }

    /** @param array<int, array<string, mixed>> $checklistData */
    public function syncChecklist(PlanContratacion $plan, array $checklistData): void
    {
        $idsMantener = [];

        foreach (array_values($checklistData) as $index => $row) {
            $descripcion = trim((string) ($row['descripcion_criterio'] ?? ''));
            if ($descripcion === '') {
                continue;
            }

            $consecutivo = $index + 1;
            $nombre = trim((string) ($row['nombre'] ?? ''));
            $payload = [
                'consecutivo' => $consecutivo,
                'nombre' => $nombre !== '' ? $nombre : mb_substr($descripcion, 0, 255),
                'descripcion_criterio' => $descripcion,
                'puntaje' => (float) ($row['puntaje'] ?? 10),
                'es_obligatorio' => filter_var($row['es_obligatorio'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'orden' => $consecutivo,
            ];

            if (! empty($row['id'])) {
                $item = ChecklistPlan::where('plan_contratacion_id', $plan->id)
                    ->where('id', $row['id'])
                    ->first();
                if ($item) {
                    $item->update($payload);
                    $idsMantener[] = $item->id;
                    continue;
                }
            }

            $nuevo = $plan->checklist()->create($payload);
            $idsMantener[] = $nuevo->id;
        }

        $plan->checklist()->whereNotIn('id', $idsMantener)->delete();
    }

    /** @param array<int, array<string, mixed>> $puntosData */
    public function syncPuntosAdicionales(PlanContratacion $plan, array $puntosData): void
    {
        $idsMantener = [];

        foreach (array_values($puntosData) as $index => $row) {
            if (empty($row['descripcion'])) {
                continue;
            }

            $consecutivo = $index + 1;
            $payload = [
                'consecutivo' => $consecutivo,
                'descripcion' => $row['descripcion'],
                'puntaje_adicional' => $row['puntaje_adicional'] ?? 0,
                'orden' => $consecutivo,
            ];

            if (! empty($row['id'])) {
                $punto = PuntoAdicional::where('plan_contratacion_id', $plan->id)
                    ->where('id', $row['id'])
                    ->first();
                if ($punto) {
                    $punto->update($payload);
                    $idsMantener[] = $punto->id;
                    continue;
                }
            }

            $nuevo = $plan->puntosAdicionales()->create($payload);
            $idsMantener[] = $nuevo->id;
        }

        $plan->puntosAdicionales()->whereNotIn('id', $idsMantener)->delete();
    }

    /** @param array<string, mixed> $row */
    private function descripcionPrograma(PlanContratacion $plan, array $row): ?string
    {
        if ($plan->tipo_registro_perfil !== 'directo') {
            return null;
        }

        $texto = trim((string) ($row['descripcion_criterio_programa'] ?? ''));

        return $texto !== '' ? $texto : null;
    }
}
