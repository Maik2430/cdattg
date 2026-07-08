<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;

trait HandlesFichaApiSearchActions
{
    public function searchFichasByNumber(Request $request)
    {
        try {
            $request->validate([
                'numero_ficha' => 'required|string|max:255',
            ]);

            $numeroFicha = $request->input('numero_ficha');

            $fichas = FichaCaracterizacion::with([
                'programaFormacion',
                'instructor.persona',
                'jornadaFormacion.parametro',
                'ambiente.piso.bloque',
                'modalidadFormacion',
                'sede',
                'diasFormacion.dia',
                'instructorFicha.instructor.persona',
            ])->where('status', true)
                ->where('ficha', 'LIKE', "%{$numeroFicha}%")
                ->orderBy('id', 'desc')
                ->get();

            if ($fichas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron fichas de caracterización con el número proporcionado',
                    'data' => [],
                ], 404);
            }

            $fichasFormateadas = $fichas->map(fn ($ficha) => $this->formatFichaForNumberSearchApi($ficha));

            return response()->json([
                'success' => true,
                'message' => 'Fichas de caracterización encontradas exitosamente',
                'data' => $fichasFormateadas,
                'total' => $fichasFormateadas->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar las fichas de caracterización',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
