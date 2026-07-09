<?php

namespace App\Http\Controllers\Concerns\Persona;

use App\Http\Requests\StorePersonaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaApiActions
{
    /**
     * Consulta una persona por número de documento (usado por Ingreso y Salida)
     */
    public function consultarPorDocumento(Request $request): JsonResponse
    {
        $request->validate([
            'cedula' => 'required|string|max:20',
        ]);

        $persona = $this->personaService->buscarPorDocumento(trim($request->cedula));

        if (! $persona) {
            return response()->json([
                'success' => false,
                'message' => 'Persona no encontrada. Complete los datos para crear un nuevo registro.',
                'data' => null,
                'show_form' => true,
            ]);
        }

        $persona->loadMissing(['caracterizacionesComplementarias']);

        return response()->json([
            'success' => true,
            'message' => 'Persona encontrada.',
            'data' => [
                'id' => $persona->id,
                'tipo_documento' => $persona->tipo_documento,
                'numero_documento' => $persona->numero_documento,
                'primer_nombre' => $persona->primer_nombre,
                'segundo_nombre' => $persona->segundo_nombre,
                'primer_apellido' => $persona->primer_apellido,
                'segundo_apellido' => $persona->segundo_apellido,
                'fecha_nacimiento' => $persona->fecha_nacimiento,
                'genero' => $persona->genero,
                'telefono' => $persona->telefono,
                'celular' => $persona->celular,
                'email' => $persona->email,
                'pais_id' => $persona->pais_id,
                'departamento_id' => $persona->departamento_id,
                'municipio_id' => $persona->municipio_id,
                'direccion' => $persona->direccion,
                'caracterizaciones' => $persona->caracterizacionesComplementarias->pluck('id')->toArray(),
            ],
            'show_form' => false,
        ]);
    }

    /**
     * Crea una persona desde una petición JSON (usado por Ingreso y Salida)
     */
    public function storeJson(StorePersonaRequest $request): JsonResponse
    {
        try {
            $persona = $this->personaService->crear($request->validated());

            Log::info('Persona creada desde Ingreso y Salida', [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'user_id' => Auth::id(),
            ]);

            $persona->loadMissing(['caracterizacionesComplementarias']);

            return response()->json([
                'success' => true,
                'message' => 'Persona creada exitosamente.',
                'data' => [
                    'tipo_documento' => $persona->tipo_documento,
                    'numero_documento' => $persona->numero_documento,
                    'primer_nombre' => $persona->primer_nombre,
                    'segundo_nombre' => $persona->segundo_nombre,
                    'primer_apellido' => $persona->primer_apellido,
                    'segundo_apellido' => $persona->segundo_apellido,
                    'fecha_nacimiento' => $persona->fecha_nacimiento,
                    'genero' => $persona->genero,
                    'telefono' => $persona->telefono,
                    'celular' => $persona->celular,
                    'email' => $persona->email,
                    'pais_id' => $persona->pais_id,
                    'departamento_id' => $persona->departamento_id,
                    'municipio_id' => $persona->municipio_id,
                    'direccion' => $persona->direccion,
                    'caracterizaciones' => $persona->caracterizacionesComplementarias->pluck('id')->toArray(),
                ],
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error de base de datos al crear persona', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la información. Por favor, verifique los datos e intente nuevamente.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error inesperado al crear persona', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado. Por favor, contacte al administrador.',
            ], 500);
        }
    }
}
