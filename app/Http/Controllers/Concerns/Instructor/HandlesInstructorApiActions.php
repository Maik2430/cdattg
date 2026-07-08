<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\CentroFormacion;
use App\Models\Persona;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait HandlesInstructorApiActions
{
    public function ApiUpdate(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $personaD = Persona::find($request->persona_id);
            if (! $personaD) {
                DB::rollBack();

                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            $personaD->update([
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'fecha_de_nacimiento' => $request->fecha_de_nacimiento,
                'genero' => $request->genero,
                'email' => $request->email,
            ]);

            $user = User::where('persona_id', $request->persona_id)->first();
            if (! $user) {
                DB::rollBack();

                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $user->update([
                'email' => $request->email,
                'password' => Hash::make($request->numero_documento),
            ]);

            DB::commit();

            $user->refresh();
            $token = $user->createToken('Token Name')->plainTextToken;
            $personaD->refresh();

            $persona = [
                'id' => $personaD->id,
                'tipo_documento' => $personaD->tipoDocumento?->name,
                'numero_documento' => $personaD->numero_documento,
                'primer_nombre' => $personaD->primer_nombre,
                'segundo_nombre' => $personaD->segundo_nombre,
                'primer_apellido' => $personaD->primer_apellido,
                'segundo_apellido' => $personaD->segundo_apellido,
                'fecha_de_nacimiento' => $personaD->fecha_de_nacimiento,
                'genero' => $personaD->tipoGenero?->name,
                'email' => $personaD->email,
                'created_at' => $personaD->created_at,
                'updated_at' => $personaD->updated_at,
                'instructor_id' => $personaD->instructor?->id,
                'regional_id' => $personaD->instructor?->regional?->id,
            ];

            return response()->json(['user' => $user, 'persona' => $persona, 'token' => $token], 200);
        } catch (QueryException $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene centros de formación por regional (AJAX)
     */
    public function centrosPorRegional(Request $request): JsonResponse
    {
        try {
            $regionalId = $request->input('regional_id');

            Log::info('Solicitud de centros por regional recibida', [
                'regional_id' => $regionalId,
                'tipo' => gettype($regionalId),
                'request_all' => $request->all(),
            ]);

            if (! $regionalId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de regional requerido',
                ], 400);
            }

            $regionalId = (int) $regionalId;

            $centros = $this->fetchCentrosFormacionPorRegional($regionalId);

            Log::info('Centros obtenidos por regional', [
                'regional_id' => $regionalId,
                'cantidad' => $centros->count(),
            ]);

            return response()->json([
                'success' => true,
                'centros' => $centros,
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener centros por regional', [
                'regional_id' => $request->input('regional_id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener centros de formación: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * @return Collection<int, array{id: int, nombre: string}>
     */
    private function fetchCentrosFormacionPorRegional(int $regionalId): Collection
    {
        return CentroFormacion::where('regional_id', $regionalId)
            ->where('status', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn ($centro) => [
                'id' => (int) $centro->id,
                'nombre' => $centro->nombre,
            ])
            ->values();
    }
}
