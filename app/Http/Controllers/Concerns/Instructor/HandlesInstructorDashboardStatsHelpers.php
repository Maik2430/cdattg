<?php

namespace App\Http\Controllers\Concerns\Instructor;

use App\Models\Instructor;

trait HandlesInstructorDashboardStatsHelpers
{
    /**
     * Obtener estadísticas de desempeño del instructor
     */
    private function obtenerEstadisticasDesempeno(Instructor $instructor): array
    {
        $resumenFichas = $this->businessRulesService->obtenerResumenFichas($instructor);
        $horasEsteMes = $this->businessRulesService->sumarHorasDelMes($instructor, now());
        $promedioHorasUltimosMeses = $this->businessRulesService->promedioHorasUltimosMeses($instructor, 6);

        return [
            'fichas_activas' => $resumenFichas['activas'],
            'fichas_proximas' => $resumenFichas['proximas'],
            'fichas_finalizadas' => $resumenFichas['finalizadas'],
            'total_horas' => $resumenFichas['total_horas'],
            'horas_este_mes' => $horasEsteMes,
            'promedio_horas_mes' => $promedioHorasUltimosMeses,
            'anos_experiencia' => $instructor->anos_experiencia ?? 0,
            'especialidades' => count($instructor->especialidades['secundarias'] ?? []) +
                (empty($instructor->especialidades['principal']) ? 0 : 1),
        ];
    }

    /**
     * Obtener notificaciones recientes para el instructor
     */
    private function obtenerNotificacionesRecientes(): array
    {
        // Simular notificaciones - en un sistema real vendrían de una tabla de notificaciones
        return [
            [
                'id' => 1,
                'titulo' => 'Nueva ficha asignada',
                'mensaje' => 'Se te ha asignado una nueva ficha de programación web',
                'tipo' => 'success',
                'fecha' => now()->subHours(2),
                'leida' => false,
            ],
            [
                'id' => 2,
                'titulo' => 'Recordatorio de clase',
                'mensaje' => 'Tienes una clase programada mañana a las 8:00 AM',
                'tipo' => 'info',
                'fecha' => now()->subHours(5),
                'leida' => false,
            ],
            [
                'id' => 3,
                'titulo' => 'Evaluación pendiente',
                'mensaje' => 'Debes completar la evaluación de la ficha 12345',
                'tipo' => 'warning',
                'fecha' => now()->subDays(1),
                'leida' => true,
            ],
        ];
    }

    /**
     * Obtener actividades recientes del instructor
     */
    private function obtenerActividadesRecientes(Instructor $instructor): array
    {
        $actividades = [];

        // Obtener fichas recientes
        $fichasRecientes = $instructor->instructorFichas()
            ->with(['ficha.programaFormacion'])
            ->whereHas('ficha')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($fichasRecientes as $instructorFicha) {
            $actividades[] = [
                'tipo' => 'ficha_asignada',
                'titulo' => 'Ficha asignada',
                'descripcion' => 'Se asignó la ficha '.$instructorFicha->ficha->ficha.' - '.
                    ($instructorFicha->ficha->programaFormacion->nombre ?? 'Sin programa'),
                'fecha' => $instructorFicha->created_at,
                'icono' => 'fas fa-clipboard-list',
            ];
        }

        return $actividades;
    }
}
