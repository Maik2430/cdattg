<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\RedConocimiento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorEspecialidadRemoveActions
{
    /**
     * Remover especialidad del instructor
     */
    public function removerEspecialidad(Request $request, Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);

        $request->validate([
            'especialidad' => 'required|integer|exists:red_conocimientos,id',
            'tipo' => 'required|in:principal,secundaria',
        ]);

        try {
            DB::beginTransaction();

            $especialidadesActuales = $instructor->especialidades ?? [];
            $especialidadId = (int) $request->especialidad;

            // Obtener el nombre de la especialidad para el mensaje
            $redConocimiento = RedConocimiento::find($especialidadId);
            $especialidadNombre = $redConocimiento ? $redConocimiento->nombre : 'N/A';

            if ($request->tipo === 'principal') {
                // Verificar que la especialidad principal existe
                if ($especialidadesActuales['principal'] != $especialidadId) {
                    return redirect()
                        ->back()
                        ->with('error', 'La especialidad principal especificada no coincide');
                }

                $especialidadesActuales['principal'] = null;
                $mensaje = "Especialidad principal '{$especialidadNombre}' removida exitosamente";

                Log::info('Especialidad principal removida', [
                    'instructor_id' => $instructor->id,
                    'especialidad_removida_id' => $especialidadId,
                    'especialidad_removida_nombre' => $especialidadNombre,
                ]);
            } else {
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];

                // Verificar que la especialidad secundaria existe
                if (! in_array($especialidadId, $especialidadesSecundarias)) {
                    return redirect()
                        ->back()
                        ->with('error', 'La especialidad secundaria especificada no existe');
                }

                // Remover la especialidad de la lista (comparar IDs, no nombres)
                $especialidadesSecundarias = array_filter(
                    $especialidadesSecundarias,
                    function ($espId) use ($especialidadId) {
                        return $espId != $especialidadId;
                    }
                );
                $especialidadesActuales['secundarias'] = array_values($especialidadesSecundarias);
                $mensaje = "Especialidad secundaria '{$especialidadNombre}' removida exitosamente";

                Log::info('Especialidad secundaria removida', [
                    'instructor_id' => $instructor->id,
                    'especialidad_removida_id' => $especialidadId,
                    'especialidad_removida_nombre' => $especialidadNombre,
                    'especialidades_restantes' => $especialidadesSecundarias,
                ]);
            }

            $instructor->especialidades = $especialidadesActuales;
            $instructor->save();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', $mensaje);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al remover especialidad', [
                'instructor_id' => $instructor->id,
                'especialidad' => $request->especialidad,
                'tipo' => $request->tipo,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al remover la especialidad. Por favor, inténtelo de nuevo.');
        }
    }
}
