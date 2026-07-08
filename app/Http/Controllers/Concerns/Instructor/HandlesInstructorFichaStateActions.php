<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorFichaStateActions
{
    /**
     * Cambiar estado del instructor
     */
    public function cambiarEstado(Request $request, Instructor $instructor)
    {
        try {
            $this->authorize('cambiarEstado', $instructor);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('Intento de cambiar estado de instructor sin autorización', [
                'user_id' => Auth::id(),
                'instructor_id' => $instructor->id,
                'user_roles' => Auth::user()->roles->pluck('name')->toArray(),
                'user_permissions' => Auth::user()->getAllPermissions()->pluck('name')->toArray(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'No tiene autorización para cambiar el estado del instructor. Se requiere el permiso "CAMBIAR ESTADO INSTRUCTOR" y el rol de Administrador o Super Administrador.');
        }

        $request->validate([
            'estado' => 'required|in:activo,inactivo',
        ]);

        try {
            // Convertir 'activo'/'inactivo' a boolean true/false
            $status = $request->estado === 'activo' ? true : false;

            $instructor->update(['status' => $status]);

            Log::info('Estado de instructor cambiado exitosamente', [
                'instructor_id' => $instructor->id,
                'status_anterior' => ! $status,
                'status_nuevo' => $status,
                'user_id' => Auth::id(),
            ]);

            $mensaje = $status
                ? 'Instructor activado exitosamente'
                : 'Instructor desactivado exitosamente';

            return redirect()
                ->back()
                ->with('success', $mensaje);
        } catch (Exception $e) {
            Log::error('Error al cambiar estado del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al cambiar el estado del instructor: '.$e->getMessage());
        }
    }
}
