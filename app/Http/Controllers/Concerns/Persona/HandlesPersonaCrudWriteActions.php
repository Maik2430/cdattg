<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Exceptions\PersonaException;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Models\Persona;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaCrudWriteActions
{
    use HandlesPersonaUpdateResponseHelpers;

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        try {
            $this->personaService->crear($request->validated());

            return redirect()->route('personas.index')->with(
                'success',
                'La persona fue creada exitosamente en el sistema.'
            );
        } catch (\Throwable $e) {
            Log::error('Error al registrar persona: '.$e->getMessage());

            return redirect()->back()->withInput()->with(
                'error',
                'No se pudo registrar la persona. Por favor, verifique los datos e inténtelo nuevamente.'
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        $expectsJson = $request->expectsJson() || $request->wantsJson();
        $successPayload = null;
        $errorMessage = null;

        try {
            $this->personaService->actualizar($persona, $request->validated());
            $successPayload = $this->buildPersonaUpdateSuccessPayload($persona);
        } catch (\Throwable $e) {
            Log::error("Error al actualizar la persona (ID: {$persona->id}): ".$e->getMessage());
            $errorMessage = 'Error al actualizar la información. Por favor, inténtelo de nuevo.';
        }

        if ($expectsJson) {
            $payload = $successPayload ?? [
                'success' => false,
                'message' => $errorMessage,
            ];

            return response()->json($payload, $successPayload ? 200 : 500);
        }

        if ($successPayload) {
            return redirect()->route('personas.show', $persona->id)
                ->with('success', $successPayload['message']);
        }

        return redirect()->back()->withErrors([
            'error' => $errorMessage.' Comuníquese con el administrador si el problema persiste.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        try {
            $this->personaService->eliminar($persona->id);

            return redirect()->route('personas.index')->with('success', 'Persona eliminada exitosamente');
        } catch (QueryException $e) {
            Log::error("Error al eliminar la persona (ID: {$persona->id}): ".$e->getMessage());

            $message = 'No se pudo eliminar la persona. Es posible que tenga un usuario asociado.';
        } catch (PersonaException $e) {
            Log::warning("No se pudo eliminar la persona (ID: {$persona->id}): ".$e->getMessage());

            $message = $e->getMessage();
        } catch (\Throwable $e) {
            Log::error("Error inesperado al eliminar la persona (ID: {$persona->id}): ".$e->getMessage());

            $message = 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.';
        }

        return redirect()->back()->with('error', $message);
    }
}
