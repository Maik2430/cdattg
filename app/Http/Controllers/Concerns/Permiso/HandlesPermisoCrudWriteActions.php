<?php

namespace App\Http\Controllers\Concerns\Permiso;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait HandlesPermisoCrudWriteActions
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $permisosUser = $request->input('permisos', []);
            $userId = $request->user_id;

            // Prevenir que el usuario modifique sus propios permisos
            if ($userId == auth()->id()) {
                return redirect()->back()->with('error', 'No puedes modificar tus propios permisos.');
            }

            $this->permisoService->asignarPermisos($userId, $permisosUser);

            return redirect()->route('permiso.index')->with('success', 'Permisos asignados con éxito');
        } catch (Exception $e) {
            Log::error('Error al asignar permisos: '.$e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
