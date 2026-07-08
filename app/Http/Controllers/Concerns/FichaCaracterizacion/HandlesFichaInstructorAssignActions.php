<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Http\Requests\AsignarInstructoresRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaInstructorAssignActions
{
    /**
     * Asigna instructores a una ficha.
     *
     * @param  string  $id  El ID de la ficha.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarInstructores(AsignarInstructoresRequest $request, string $id)
    {
        try {
            // El FormRequest ya maneja todas las validaciones
            $instructoresData = $request->validated()['instructores'];
            $instructorPrincipalId = $request->validated()['instructor_principal_id'];

            Log::info('Iniciando asignación de instructores con validaciones robustas', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'instructores_count' => count($instructoresData),
                'instructor_principal_id' => $instructorPrincipalId,
                'timestamp' => now(),
            ]);

            // Usar el servicio especializado para la asignación
            $asignacionService = app(\App\Services\AsignacionInstructorService::class);
            $resultado = $asignacionService->asignarInstructores(
                $instructoresData,
                $id,
                $instructorPrincipalId,
                Auth::id()
            );

            if ($resultado['success']) {
                return redirect()->route('fichaCaracterizacion.gestionarInstructores', $id)
                    ->with('success', $resultado['message'].' Se asignaron '.$resultado['total_asignados'].' instructores.');
            } else {
                return back()
                    ->withErrors(['error' => $resultado['message']])
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en asignación de instructores', [
                'ficha_id' => $id,
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Error crítico en asignación de instructores', [
                'ficha_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withErrors(['error' => 'Error crítico al asignar instructores. Por favor, contacte al administrador.'])
                ->withInput();
        }
    }

    /**
     * Desasigna un instructor de una ficha.
     *
     * @param  string  $id  El ID de la ficha.
     * @param  string  $instructorId  El ID del instructor.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function desasignarInstructor(string $id, string $instructorId)
    {
        try {
            Log::info('Iniciando desasignación de instructor con servicio robusto', [
                'user_id' => Auth::id(),
                'ficha_id' => $id,
                'instructor_id' => $instructorId,
                'timestamp' => now(),
            ]);

            // Usar el servicio especializado para la desasignación
            $asignacionService = app(\App\Services\AsignacionInstructorService::class);
            $resultado = $asignacionService->desasignarInstructor(
                (int) $instructorId,
                (int) $id,
                Auth::id()
            );

            if ($resultado['success']) {
                return back()->with('success', $resultado['message']);
            } else {
                return back()->withErrors(['error' => $resultado['message']]);
            }

        } catch (\Exception $e) {
            Log::error('Error crítico al desasignar instructor', [
                'ficha_id' => $id,
                'instructor_id' => $instructorId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'Error crítico al desasignar instructor. Por favor, contacte al administrador.']);
        }
    }
}
