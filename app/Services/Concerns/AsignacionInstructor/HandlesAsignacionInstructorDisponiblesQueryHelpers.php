<?php

namespace App\Services\Concerns\AsignacionInstructor;

use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

trait HandlesAsignacionInstructorDisponiblesQueryHelpers
{
    protected function cargarFichaParaDisponibles(int $fichaId): FichaCaracterizacion
    {
        return FichaCaracterizacion::with([
            'programaFormacion.redConocimiento',
            'diasFormacion',
            'sede.regional',
            'jornadaFormacion.parametro',
            'modalidadFormacion',
        ])->findOrFail($fichaId);
    }

    protected function prepararDatosFichaDisponibles(FichaCaracterizacion $ficha): array
    {
        $regionalId = $ficha->sede->regional_id ?? null;
        $redConocimientoId = $ficha->programaFormacion->red_conocimiento_id ?? null;

        return [
            'fecha_inicio' => $ficha->fecha_inicio,
            'fecha_fin' => $ficha->fecha_fin,
            'especialidad_requerida' => $ficha->programaFormacion->redConocimiento->nombre ?? null,
            'especialidad_requerida_id' => $redConocimientoId,
            'instructor_lider_id' => $ficha->instructor_id,
            'regional_id' => $regionalId,
            'jornada_id' => $ficha->jornada_id,
            'modalidad_id' => $ficha->modalidad_formacion_id,
            'horas_semanales' => 0,
            'red_conocimiento_id' => $redConocimientoId,
            'ficha_jornada_id' => $ficha->jornada_id,
            'ficha_modalidad_id' => $ficha->modalidad_formacion_id,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Instructor>
     */
    protected function construirQueryInstructoresDisponibles(
        FichaCaracterizacion $ficha,
        array $contexto
    ): \Illuminate\Database\Eloquent\Collection {
        $regionalId = $contexto['regional_id'];
        $redConocimientoId = $contexto['red_conocimiento_id'];
        $instructorLiderId = $contexto['instructor_lider_id'];

        $instructores = Instructor::with(['persona', 'regional', 'jornadas.parametro', 'modalidades.parametro'])
            ->where('status', true);

        if ($regionalId) {
            $instructores->where('regional_id', $regionalId);
        }

        if ($redConocimientoId !== null) {
            $instructores->where(function (Builder $query) use ($redConocimientoId, $instructorLiderId) {
                if ($instructorLiderId) {
                    $query->where('id', $instructorLiderId);
                }

                $query->orWhere(function (Builder $q) use ($redConocimientoId) {
                    $q->where('especialidades->principal', $redConocimientoId)
                        ->orWhereJsonContains('especialidades->secundarias', $redConocimientoId);
                });
            });

            Log::info('Filtro por red de conocimiento aplicado', [
                'red_conocimiento_id' => $redConocimientoId,
                'instructor_lider_id' => $instructorLiderId,
            ]);
        } else {
            Log::info('No se aplicó filtro por red de conocimiento (red_conocimiento_id es null)');
        }

        $resultado = $instructores->get();

        Log::info('Instructores encontrados para ficha con filtros', [
            'ficha_id' => $ficha->id,
            'regional_id' => $regionalId,
            'red_conocimiento_id' => $redConocimientoId,
            'jornada_id_ficha' => $contexto['ficha_jornada_id'],
            'modalidad_id_ficha' => $contexto['ficha_modalidad_id'],
            'instructor_lider_id' => $instructorLiderId,
            'total_instructores_filtrados' => $resultado->count(),
            'instructores_filtrados_ids' => $resultado->pluck('id')->toArray(),
            'instructores_con_detalles' => $resultado->map(function ($inst) {
                return [
                    'id' => $inst->id,
                    'nombre' => $inst->persona->nombre_completo ?? 'N/A',
                    'regional_id' => $inst->regional_id,
                    'jornadas_json' => $inst->jornadas,
                    'jornadas_relacion_count' => $inst->jornadas()->count(),
                    'especialidades' => $inst->especialidades,
                    'status' => $inst->status,
                ];
            })->toArray(),
        ]);

        return $resultado;
    }
}
