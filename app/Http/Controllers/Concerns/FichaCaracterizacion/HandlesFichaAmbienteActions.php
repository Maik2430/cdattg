<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesFichaAmbienteActions
{
    /**
     * Obtiene los ambientes disponibles filtrados por sede.
     *
     * @param  int  $sedeId  ID de la sede
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAmbientesPorSede($sedeId)
    {
        try {
            Log::info('Obteniendo ambientes por sede', [
                'sede_id' => $sedeId,
                'user_id' => Auth::id(),
            ]);

            $ambientes = \App\Models\Ambiente::with(['piso.bloque'])
                ->whereHas('piso.bloque', function ($query) use ($sedeId) {
                    $query->where('sede_id', $sedeId);
                })
                ->where('status', 1) // Solo ambientes activos
                ->orderBy('title', 'asc')
                ->get();

            $ambientesFormateados = $ambientes->map(function ($ambiente) {
                return [
                    'id' => $ambiente->id,
                    'title' => $ambiente->title,
                    'descripcion' => $ambiente->piso ?
                        $ambiente->piso->bloque->nombre.' - '.$ambiente->piso->nombre :
                        'Sin ubicación',
                ];
            });

            Log::info('Ambientes obtenidos exitosamente', [
                'sede_id' => $sedeId,
                'total_ambientes' => $ambientesFormateados->count(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $ambientesFormateados,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener ambientes por sede', [
                'sede_id' => $sedeId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los ambientes de la sede.',
            ], 500);
        }
    }
}
