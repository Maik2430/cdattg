<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use App\Models\RedConocimiento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorEspecialidadAssignActions
{
    /**
     * Asignar especialidad al instructor
     */
    public function asignarEspecialidad(Request $request, Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);

        $request->validate([
            'red_conocimiento_id' => 'required|exists:red_conocimientos,id',
            'tipo' => 'required|in:principal,secundaria',
        ]);

        try {
            DB::beginTransaction();

            // Validar que la red de conocimiento pertenezca a la regional del instructor
            $redConocimiento = RedConocimiento::where('id', $request->red_conocimiento_id)
                ->where('regionals_id', $instructor->regional_id)
                ->where('status', true)
                ->first();

            if (! $redConocimiento) {
                return redirect()
                    ->back()
                    ->with('error', 'La red de conocimiento no está disponible para esta regional');
            }

            $especialidadesActuales = $instructor->especialidades ?? [];
            $redConocimientoId = $redConocimiento->id;

            if ($request->tipo === 'principal') {
                // Verificar que no esté ya asignada como secundaria
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];
                if (in_array($redConocimientoId, $especialidadesSecundarias)) {
                    // Remover de secundarias antes de asignar como principal
                    $especialidadesSecundarias = array_filter(
                        $especialidadesSecundarias,
                        function ($espId) use ($redConocimientoId) {
                            return $espId !== $redConocimientoId;
                        }
                    );
                    $especialidadesActuales['secundarias'] = array_values($especialidadesSecundarias);
                }

                // Solo puede haber una especialidad principal (guardar ID, no nombre)
                $especialidadesActuales['principal'] = $redConocimientoId;
                $mensaje = "Especialidad principal '{$redConocimiento->nombre}' asignada exitosamente";
            } else {
                // Agregar especialidad secundaria
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];

                // Verificar que no sea la misma especialidad principal
                if ($especialidadesActuales['principal'] == $redConocimientoId) {
                    return redirect()
                        ->back()
                        ->with('warning', 'Esta especialidad ya está asignada como principal');
                }

                // Verificar que no esté ya en secundarias
                if (in_array($redConocimientoId, $especialidadesSecundarias)) {
                    return redirect()
                        ->back()
                        ->with('warning', 'Esta especialidad ya está asignada como secundaria');
                }

                $especialidadesSecundarias[] = $redConocimientoId;
                $especialidadesActuales['secundarias'] = $especialidadesSecundarias;
                $mensaje = "Especialidad secundaria '{$redConocimiento->nombre}' asignada exitosamente";
            }

            $instructor->especialidades = $especialidadesActuales;
            $instructor->save();

            DB::commit();

            Log::info('Especialidad asignada exitosamente', [
                'instructor_id' => $instructor->id,
                'especialidad' => $redConocimiento->nombre,
                'tipo' => $request->tipo,
                'especialidades_actuales' => $especialidadesActuales,
            ]);

            return redirect()
                ->back()
                ->with('success', $mensaje);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al asignar especialidad', [
                'instructor_id' => $instructor->id,
                'red_conocimiento_id' => $request->red_conocimiento_id,
                'tipo' => $request->tipo,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al asignar la especialidad. Por favor, inténtelo de nuevo.');
        }
    }
}
