<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorUpdateSyncHelpers
{
    private function syncInstructorJornadasOnUpdate(Instructor $instructor, array $jornadasIds): void
    {
        try {
            if (! empty($jornadasIds)) {
                $pivotData = [];
                foreach ($jornadasIds as $jornadaId) {
                    $pivotData[$jornadaId] = [
                        'user_edit_id' => Auth::id(),
                        'updated_at' => now(),
                    ];
                }
                Log::info('Sincronizando jornadas con pivot data', [
                    'instructor_id' => $instructor->id,
                    'pivot_data' => $pivotData,
                ]);

                $resultado = $instructor->jornadas()->sync($pivotData);

                $instructor->jornadas = $jornadasIds;
                $instructor->save();

                Log::info('Jornadas sincronizadas exitosamente', [
                    'instructor_id' => $instructor->id,
                    'resultado_sync' => $resultado,
                    'jornadas_actuales' => $instructor->jornadas()->pluck('parametros_temas.id')->toArray(),
                ]);
            } else {
                Log::info('No hay jornadas seleccionadas, eliminando todas', [
                    'instructor_id' => $instructor->id,
                ]);
                $instructor->jornadas()->detach();
                $instructor->jornadas = null;
                $instructor->save();
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar jornadas del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'jornadas_ids' => $jornadasIds,
            ]);
        }
    }

    private function syncInstructorModalidadesOnUpdate(Instructor $instructor, array $modalidadesIds): void
    {
        try {
            if (! empty($modalidadesIds)) {
                $pivotData = [];
                foreach ($modalidadesIds as $modalidadId) {
                    $pivotData[$modalidadId] = [
                        'user_edit_id' => Auth::id(),
                        'updated_at' => now(),
                    ];
                }
                Log::info('Sincronizando modalidades con pivot data', [
                    'instructor_id' => $instructor->id,
                    'pivot_data' => $pivotData,
                ]);

                $instructor->modalidades()->sync($pivotData);

                Log::info('Modalidades sincronizadas exitosamente', [
                    'instructor_id' => $instructor->id,
                    'modalidades_actuales' => $instructor->modalidades()->pluck('parametros_temas.id')->toArray(),
                ]);

                $instructor->setAttribute('habilidades_pedagogicas', $modalidadesIds);
                $instructor->save();

                $instructor->refresh();

                Log::info('Campo JSON habilidades_pedagogicas actualizado', [
                    'instructor_id' => $instructor->id,
                    'habilidades_pedagogicas' => $instructor->habilidades_pedagogicas,
                    'habilidades_pedagogicas_raw' => $instructor->getRawOriginal('habilidades_pedagogicas'),
                ]);
            } else {
                Log::info('No hay modalidades seleccionadas, eliminando todas', [
                    'instructor_id' => $instructor->id,
                ]);
                $instructor->modalidades()->detach();

                $instructor->setAttribute('habilidades_pedagogicas', null);
                $instructor->save();
                $instructor->refresh();
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar modalidades del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
                'modalidades_ids' => $modalidadesIds,
            ]);
        }
    }
}
