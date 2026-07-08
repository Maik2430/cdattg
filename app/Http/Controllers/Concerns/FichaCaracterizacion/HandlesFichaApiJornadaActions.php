<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;

trait HandlesFichaApiJornadaActions
{
    public function getFichasCaracterizacionPorJornada(Request $request)
    {
        try {
            $jornadaId = $request->route('jornadaId') ?? $request->route('id') ?? $request->input('id');

            $fichas = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'jornadaFormacion.parametro',
                'ambiente.piso.bloque',
                'modalidadFormacion',
                'sede',
                'diasFormacion.dia',
                'instructorFicha.instructor.persona',
            ])->withCount('aprendices')
                ->where('status', true)
                ->where('jornada_id', $jornadaId)
                ->orderBy('id', 'desc')
                ->get();

            if ($fichas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron fichas de caracterización para la jornada especificada',
                    'data' => [],
                    'id' => $jornadaId,
                ], 404);
            }

            $fichasFormateadas = $fichas->map(fn ($ficha) => $this->formatFichaForJornadaApi($ficha));

            return response()->json([
                'success' => true,
                'message' => 'Fichas de caracterización obtenidas exitosamente por jornada',
                'data' => $fichasFormateadas,
                'total' => $fichasFormateadas->count(),
                'jornada_id' => $jornadaId,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las fichas de caracterización por jornada',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
