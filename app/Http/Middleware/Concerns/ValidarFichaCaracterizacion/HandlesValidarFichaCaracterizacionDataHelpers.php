<?php

namespace App\Http\Middleware\Concerns\ValidarFichaCaracterizacion;

use App\Models\FichaCaracterizacion;
use Illuminate\Http\Request;

trait HandlesValidarFichaCaracterizacionDataHelpers
{
    private function debeValidar(Request $request, $action): bool
    {
        $routeName = $request->route()->getName();

        $rutasValidadas = [
            'fichaCaracterizacion.store',
            'fichaCaracterizacion.update',
            'fichaCaracterizacion.asignarInstructores',
            'fichaCaracterizacion.guardarDiasFormacion',
        ];

        return in_array($routeName, $rutasValidadas);
    }

    private function obtenerDatosFicha(Request $request, $action): array
    {
        $datos = [];

        switch ($action) {
            case 'store':
            case 'update':
                $datos = $request->only([
                    'ficha',
                    'programa_formacion_id',
                    'fecha_inicio',
                    'fecha_fin',
                    'instructor_id',
                    'ambiente_id',
                    'sede_id',
                    'jornada_id',
                    'modalidad_formacion_id',
                    'total_horas',
                ]);
                break;

            case 'asignarInstructores':
            case 'guardarDiasFormacion':
                $fichaId = $request->route('id');
                if ($fichaId) {
                    $ficha = FichaCaracterizacion::find($fichaId);
                    if ($ficha) {
                        $datos = $ficha->toArray();
                    }
                }
                break;
        }

        return $datos;
    }

    private function obtenerFichaId(Request $request, $action)
    {
        if ($action === 'update') {
            return $request->route('fichaCaracterizacion') ?? $request->route('id');
        }

        return null;
    }

    private function manejarErroresValidacion(Request $request, $errores)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación encontrados',
                'errors' => $errores,
            ], 422);
        }

        return back()->withErrors([
            'validacion_negocio' => $errores,
        ])->withInput();
    }
}
