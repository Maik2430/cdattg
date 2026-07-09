<?php

namespace App\Http\Controllers\Concerns\FichaCaracterizacionFlutter;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;

trait HandlesFichaCaracterizacionFlutterApiActions
{
    use HandlesFichaCaracterizacionFlutterQueryHelpers;

    /**
     * Obtener todas las fichas de caracterización para Flutter
     */
    public function getAllFichasCaracterizacion()
    {
        try {
            // Primero obtener solo los datos básicos
            $fichas = FichaCaracterizacion::select($this->getFichaCaracterizacionBaseSelect())
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $fichas,
                'total' => $fichas->count(),
            ]);
        } catch (\Exception $e) {
            return $this->buildFichaCaracterizacionErrorResponse(
                'Error al obtener las fichas de caracterización',
                $e
            );
        }
    }

    /**
     * Obtener una ficha de caracterización específica por ID
     */
    public function getFichaCaracterizacionById($id)
    {
        try {
            $ficha = FichaCaracterizacion::select($this->getFichaCaracterizacionBaseSelect())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $ficha,
            ]);
        } catch (\Exception $e) {
            return $this->buildFichaCaracterizacionErrorResponse(
                'Ficha de caracterización no encontrada',
                $e,
                404
            );
        }
    }

    /**
     * Buscar fichas de caracterización por número
     */
    public function searchFichasByNumber(Request $request)
    {
        try {
            $request->validate([
                'numero' => 'required|string|min:1',
            ]);

            $numero = $request->input('numero');

            $fichas = FichaCaracterizacion::where('ficha', 'like', "%{$numero}%")
                ->with([
                    'programaFormacion:id,nombre,codigo',
                    'instructor.persona:id,primer_nombre,primer_apellido',
                    'sede:id,nombre',
                ])
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $fichas,
                'total' => $fichas->count(),
            ]);
        } catch (\Exception $e) {
            return $this->buildFichaCaracterizacionErrorResponse(
                'Error al buscar fichas de caracterización',
                $e
            );
        }
    }

    /**
     * Obtener fichas de caracterización por jornada
     */
    public function getFichasCaracterizacionPorJornada($jornadaId)
    {
        try {
            $fichas = FichaCaracterizacion::where('jornada_id', $jornadaId)
                ->with([
                    'programaFormacion:id,nombre,codigo',
                    'instructor.persona:id,primer_nombre,primer_apellido',
                    'sede:id,nombre',
                    'jornadaFormacion.parametro:id,name',
                ])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $fichas,
                'total' => $fichas->count(),
            ]);
        } catch (\Exception $e) {
            return $this->buildFichaCaracterizacionErrorResponse(
                'Error al obtener fichas por jornada',
                $e
            );
        }
    }
}
