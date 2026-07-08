<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesFichaAprendicesAssignActions
{
    /**
     * Asigna aprendices a una ficha de caracterización.
     *
     * @param  int  $id  ID de la ficha de caracterización
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarAprendices(Request $request, $id)
    {
        try {
            $request->validate([
                'personas' => 'required|array|min:1',
                'personas.*' => 'exists:personas,id',
            ]);

            Log::info('Iniciando asignación de personas como aprendices', [
                'ficha_id' => $id,
                'personas_ids' => $request->input('personas'),
                'user_id' => Auth::id(),
            ]);

            $ficha = FichaCaracterizacion::findOrFail($id);
            $personasIds = $request->input('personas');

            DB::beginTransaction();

            // Obtener el rol de APRENDIZ
            $rolAprendiz = \Spatie\Permission\Models\Role::where('name', 'APRENDIZ')->first();

            if (! $rolAprendiz) {
                DB::rollBack();

                return redirect()->back()
                    ->with('error', 'El rol de APRENDIZ no existe en el sistema.');
            }

            // Procesar cada persona
            foreach ($personasIds as $personaId) {
                $persona = \App\Models\Persona::with('user')->findOrFail($personaId);

                // Crear o actualizar registro de aprendiz
                // Si el aprendiz existe pero está eliminado (soft delete), restaurarlo primero
                $aprendiz = \App\Models\Aprendiz::withTrashed()
                    ->where('persona_id', $personaId)
                    ->first();

                if ($aprendiz && $aprendiz->trashed()) {
                    // Restaurar el aprendiz eliminado
                    $aprendiz->restore();
                    // Actualizar los datos
                    $aprendiz->update([
                        'ficha_caracterizacion_id' => $id,
                        'estado' => 1,
                        'user_edit_id' => Auth::id(),
                    ]);
                } else {
                    // Crear o actualizar el aprendiz
                    $aprendiz = \App\Models\Aprendiz::updateOrCreate([
                        'persona_id' => $personaId,
                    ], [
                        'ficha_caracterizacion_id' => $id, // Asignar la ficha actual
                        'estado' => 1,
                        'user_create_id' => Auth::id(),
                        'user_edit_id' => Auth::id(),
                    ]);
                }

                // Asignar el rol APRENDIZ al usuario si tiene usuario asociado
                // Esto se hace siempre, independientemente de si ya estaba asignado o no
                if ($persona->user) {
                    // Verificar que el rol APRENDIZ existe
                    $rolAprendiz = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'APRENDIZ']);
                    // Asignar rol si no lo tiene (usar assignRole en lugar de syncRoles para no eliminar otros roles)
                    if (! $persona->user->hasRole('APRENDIZ')) {
                        $persona->user->assignRole('APRENDIZ');

                        Log::info('Rol APRENDIZ asignado al usuario', [
                            'user_id' => $persona->user->id,
                            'persona_id' => $personaId,
                            'aprendiz_id' => $aprendiz->id,
                            'ficha_id' => $id,
                        ]);
                    }
                }
            }

            DB::commit();

            // Refrescar la relación para asegurar que se carguen los nuevos aprendices
            $ficha->load('aprendices.persona');

            Log::info('Personas asignadas como aprendices exitosamente', [
                'ficha_id' => $id,
                'personas_asignadas' => count($personasIds),
                'aprendices_count' => $ficha->aprendices->count(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('fichaCaracterizacion.gestionarAprendices', $id)
                ->with('success', 'Personas asignadas como aprendices exitosamente a la ficha.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación al asignar personas como aprendices', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al asignar personas como aprendices', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'Error al asignar personas como aprendices. Por favor, intente nuevamente.');
        }
    }
}
