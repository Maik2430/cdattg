<?php

namespace App\Services\Concerns\AsignacionInstructor;

use Illuminate\Support\Facades\Log;

trait HandlesAsignacionInstructorDisponiblesActions
{
    public function obtenerInstructoresDisponibles(int $fichaId): array
    {
        try {
            $ficha = $this->cargarFichaParaDisponibles($fichaId);
            $contexto = $this->prepararDatosFichaDisponibles($ficha);
            $instructores = $this->construirQueryInstructoresDisponibles($ficha, $contexto);

            $disponibles = [];
            foreach ($instructores as $instructor) {
                $disponibles[] = $this->evaluarDisponibilidadInstructor($instructor, $ficha, $contexto, $fichaId);
            }

            Log::info('Instructores disponibles procesados', [
                'ficha_id' => $fichaId,
                'total_disponibles' => count($disponibles),
                'disponibles_ids' => array_map(fn ($d) => $d['instructor']->id, $disponibles),
                'disponibles_con_detalles' => array_map(function ($d) {
                    return [
                        'id' => $d['instructor']->id,
                        'nombre' => $d['instructor']->persona->nombre_completo ?? 'N/A',
                        'disponible' => $d['disponible'],
                        'razones_no_disponible' => $d['razones_no_disponible'] ?? [],
                        'advertencias' => $d['advertencias'] ?? [],
                    ];
                }, $disponibles),
            ]);

            return $disponibles;
        } catch (\Exception $e) {
            Log::error('Error obteniendo instructores disponibles', [
                'ficha_id' => $fichaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [];
        }
    }
}
