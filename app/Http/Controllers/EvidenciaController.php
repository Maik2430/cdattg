<?php

namespace App\Http\Controllers;

use App\Models\Evidencias;
use Illuminate\Http\Request;

class EvidenciaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'caracterizacion_id' => 'required|integer',
                'ficha_id' => 'required|integer',
            ]);

            // Crear la evidencia sin dependencias de competencias
            $evidencia = Evidencias::create([
                'nombre' => $request->nombre,
                'id_estado' => 1, // Estado activo por defecto
                'fecha_evidencia' => now(),
                'user_create_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Evidencia creada exitosamente',
                'evidencia_id' => $evidencia->id,
                'evidencia' => $evidencia
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }
}
