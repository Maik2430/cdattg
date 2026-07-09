<?php

namespace App\Services\Concerns\Carnet;

use App\Models\Aprendiz;
use App\Models\Instructor;
use Illuminate\Support\Facades\Log;

trait HandlesCarnetVerificacionActions
{
    public function verificarCarnet(string $qrData): array
    {
        try {
            $datos = json_decode($qrData, true);

            if (! $datos || ! isset($datos['tipo']) || ! isset($datos['id'])) {
                return [
                    'valido' => false,
                    'mensaje' => 'Código QR inválido',
                ];
            }

            if ($datos['tipo'] === 'APRENDIZ') {
                $aprendiz = Aprendiz::with('persona', 'fichaCaracterizacion')->find($datos['id']);

                if (! $aprendiz) {
                    return [
                        'valido' => false,
                        'mensaje' => 'Aprendiz no encontrado',
                    ];
                }

                return [
                    'valido' => true,
                    'tipo' => 'APRENDIZ',
                    'datos' => [
                        'nombre' => $aprendiz->persona->nombre_completo,
                        'documento' => $aprendiz->persona->numero_documento,
                        'ficha' => $aprendiz->fichaCaracterizacion->ficha ?? 'N/A',
                        'estado' => $aprendiz->estado ? 'Activo' : 'Inactivo',
                    ],
                ];
            }

            if ($datos['tipo'] === 'INSTRUCTOR') {
                $instructor = Instructor::with('persona', 'regional')->find($datos['id']);

                if (! $instructor) {
                    return [
                        'valido' => false,
                        'mensaje' => 'Instructor no encontrado',
                    ];
                }

                return [
                    'valido' => true,
                    'tipo' => 'INSTRUCTOR',
                    'datos' => [
                        'nombre' => $instructor->persona->nombre_completo,
                        'documento' => $instructor->persona->numero_documento,
                        'regional' => $instructor->regional->nombre ?? 'N/A',
                        'estado' => $instructor->status ? 'Activo' : 'Inactivo',
                    ],
                ];
            }

            return [
                'valido' => false,
                'mensaje' => 'Tipo de carnet desconocido',
            ];
        } catch (\Exception $e) {
            Log::error('Error verificando carnet', [
                'error' => $e->getMessage(),
            ]);

            return [
                'valido' => false,
                'mensaje' => 'Error al verificar carnet',
            ];
        }
    }
}
