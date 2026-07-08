<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaAprendicesGestionActions
{
    /**
     * Muestra la vista para gestionar aprendices de una ficha.
     *
     * @param  int  $id  ID de la ficha de caracterización
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function gestionarAprendices($id)
    {
        try {
            Log::info('Acceso a gestión de aprendices', [
                'ficha_id' => $id,
                'user_id' => Auth::id(),
            ]);

            $ficha = FichaCaracterizacion::with([
                'programaFormacion',
                'aprendices.persona.user',
            ])->findOrFail($id);

            // Obtener todas las personas que NO están asignadas a esta ficha
            // Estrategia: obtener personas que no están asignadas a esta ficha
            $aprendicesAsignadosIds = $ficha->aprendices()->pluck('id');

            $personasDisponibles = \App\Models\Persona::with('user', 'aprendiz')
                ->where('status', 1) // Solo personas activas
                ->where(function ($query) use ($aprendicesAsignadosIds) {
                    // Personas que no tienen aprendiz asignado a esta ficha
                    if ($aprendicesAsignadosIds->count() > 0) {
                        $query->whereDoesntHave('aprendiz', function ($subQuery) use ($aprendicesAsignadosIds) {
                            $subQuery->whereIn('id', $aprendicesAsignadosIds);
                        });
                    }

                    // Personas sin rol APRENDIZ O aprendices desasignados
                    $query->where(function ($subQuery) {
                        // Sin rol APRENDIZ
                        $subQuery->whereHas('user', function ($userQuery) {
                            $userQuery->whereDoesntHave('roles', function ($roleQuery) {
                                $roleQuery->where('name', 'APRENDIZ');
                            });
                        })
                        // O aprendices desasignados (estado = 0)
                            ->orWhereHas('aprendiz', function ($aprendizQuery) {
                                $aprendizQuery->where('estado', 0);
                            })
                        // O sin registro de aprendiz
                            ->orWhereDoesntHave('aprendiz');
                    });
                })
                ->orderBy('id', 'desc')
                ->get();

            // Debug: Obtener algunos aprendices desasignados para verificar
            $aprendicesDesasignados = \App\Models\Aprendiz::where('estado', 0)->with('persona')->get();

            Log::info('Vista de gestión de aprendices cargada', [
                'ficha_id' => $id,
                'aprendices_asignados' => $ficha->aprendices->count(),
                'personas_disponibles' => $personasDisponibles->count(),
                'aprendices_desasignados_total' => $aprendicesDesasignados->count(),
                'aprendices_desasignados_ids' => $aprendicesDesasignados->pluck('id')->toArray(),
                'user_id' => Auth::id(),
            ]);

            return view('fichas.gestionar-aprendices', compact('ficha', 'personasDisponibles'));

        } catch (\Exception $e) {
            Log::error('Error al cargar gestión de aprendices', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('fichaCaracterizacion.show', $id)
                ->with('error', 'Error al cargar la gestión de aprendices.');
        }
    }
}
