<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaAprendicesDesassignTransactionHelpers
{
    private function ejecutarDesasignacionAprendices(FichaCaracterizacion $ficha, array $personasIds, $id): \Illuminate\Http\RedirectResponse
    {
        DB::beginTransaction();

        try {
            $aprendicesAsignados = $ficha->aprendices()
                ->whereHas('persona', function ($query) use ($personasIds) {
                    $query->whereIn('id', $personasIds);
                })
                ->get();

            Log::info('Aprendices asignados a esta ficha encontrados', [
                'ficha_id' => $id,
                'personas_solicitadas' => count($personasIds),
                'personas_ids' => $personasIds,
                'aprendices_encontrados' => $aprendicesAsignados->count(),
                'aprendices_ids' => $aprendicesAsignados->pluck('id')->toArray(),
                'aprendices_persona_ids' => $aprendicesAsignados->pluck('persona_id')->toArray(),
                'user_id' => Auth::id(),
            ]);

            $personasEncontradas = $aprendicesAsignados->pluck('persona_id')->toArray();
            $personasNoEncontradas = array_diff($personasIds, $personasEncontradas);

            if (! empty($personasNoEncontradas)) {
                Log::warning('Personas no encontradas como aprendices en esta ficha', [
                    'ficha_id' => $id,
                    'personas_no_encontradas' => $personasNoEncontradas,
                    'personas_solicitadas' => $personasIds,
                    'user_id' => Auth::id(),
                ]);

                DB::rollBack();

                return redirect()->back()
                    ->with('error', 'Algunas personas no están asignadas como aprendices a esta ficha. Personas ID: '.implode(', ', $personasNoEncontradas));
            }

            $aprendicesIds = $aprendicesAsignados->pluck('id');

            Log::info('IDs de aprendices a desasignar', [
                'ficha_id' => $id,
                'aprendices_ids' => $aprendicesIds->toArray(),
                'total' => $aprendicesIds->count(),
                'user_id' => Auth::id(),
            ]);

            $aprendicesActualizados = [];
            foreach ($aprendicesIds as $aprendizId) {
                try {
                    $resultado = $this->desasignarAprendizIndividual((int) $aprendizId, (string) $id);
                    if ($resultado !== null) {
                        $aprendicesActualizados[] = $resultado;
                    }
                } catch (\Exception $e) {
                    Log::error('Error al actualizar aprendiz individual', [
                        'aprendiz_id' => $aprendizId,
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'user_id' => Auth::id(),
                    ]);
                    throw $e;
                }
            }

            DB::commit();

            Log::info('=== DESASIGNACIÓN COMPLETADA EXITOSAMENTE ===', [
                'ficha_id' => $id,
                'personas_desasignadas' => count($personasIds),
                'aprendices_actualizados' => $aprendicesActualizados,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('fichaCaracterizacion.gestionarAprendices', $id)
                ->with('success', 'Personas desasignadas como aprendices exitosamente de la ficha. Total: '.count($personasIds));
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
