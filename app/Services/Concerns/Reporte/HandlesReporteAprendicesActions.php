<?php

namespace App\Services\Concerns\Reporte;

use Illuminate\Support\Facades\Log;

trait HandlesReporteAprendicesActions
{
    /**
     * @return mixed
     */
    public function generarReporteAprendices(int $fichaId, string $formato = 'array')
    {
        try {
            $aprendices = $this->aprendizRepo->obtenerPorFicha($fichaId);
            $ficha = $this->fichaRepo->encontrarConRelaciones($fichaId);

            $datos = [
                'ficha' => [
                    'numero' => $ficha->ficha ?? 'N/A',
                    'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                ],
                'total_aprendices' => $aprendices->count(),
                'aprendices_activos' => $aprendices->where('estado', true)->count(),
                'aprendices' => $aprendices->map(function ($aprendiz) {
                    return [
                        'documento' => $aprendiz->persona->numero_documento,
                        'nombre' => $aprendiz->persona->nombre_completo,
                        'email' => $aprendiz->persona->email,
                        'estado' => $aprendiz->estado ? 'Activo' : 'Inactivo',
                    ];
                }),
            ];

            return match ($formato) {
                'excel' => $this->generarExcelAprendices($datos),
                'pdf' => $this->generarPDFAprendices($datos),
                default => $datos,
            };
        } catch (\Exception $e) {
            Log::error('Error generando reporte de aprendices', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
