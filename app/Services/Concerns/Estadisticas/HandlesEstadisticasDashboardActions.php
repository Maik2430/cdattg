<?php

namespace App\Services\Concerns\Estadisticas;

trait HandlesEstadisticasDashboardActions
{
    public function obtenerDashboardGeneral(): array
    {
        return $this->cache('dashboard.general', function () {
            $superAdminUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'SUPER ADMINISTRADOR');
            })->get();

            $adminUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'ADMINISTRADOR');
            })->get();

            $instructorUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'INSTRUCTOR');
            })->get();

            $visitanteUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'VISITANTE');
            })->get();

            $aprendizUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'APRENDIZ');
            })->get();

            $aspiranteUsers = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'ASPIRANTE');
            })->get();

            return [
                'roles' => [
                    'super_administradores' => [
                        'total' => $superAdminUsers->count(),
                        'activos' => $superAdminUsers->where('status', true)->count(),
                        'inactivos' => $superAdminUsers->where('status', false)->count(),
                    ],
                    'administradores' => [
                        'total' => $adminUsers->count(),
                        'activos' => $adminUsers->where('status', true)->count(),
                        'inactivos' => $adminUsers->where('status', false)->count(),
                    ],
                    'instructores' => [
                        'total' => \App\Models\Instructor::count(),
                        'activos' => \App\Models\Instructor::where('status', true)->count(),
                        'inactivos' => \App\Models\Instructor::where('status', false)->count(),
                    ],
                    'visitantes' => [
                        'total' => $visitanteUsers->count(),
                        'activos' => $visitanteUsers->where('status', true)->count(),
                        'inactivos' => $visitanteUsers->where('status', false)->count(),
                    ],
                    'aprendices' => [
                        'total' => \App\Models\Aprendiz::count(),
                        'activos' => \App\Models\Aprendiz::where('estado', true)->count(),
                        'inactivos' => \App\Models\Aprendiz::where('estado', false)->count(),
                    ],
                    'aspirantes' => [
                        'total' => $aspiranteUsers->count(),
                        'activos' => $aspiranteUsers->where('status', true)->count(),
                        'inactivos' => $aspiranteUsers->where('status', false)->count(),
                    ],
                ],
                'asistencias_hoy' => \App\Models\AsistenciaAprendiz::whereDate('created_at', today())->count(),
                'personas_dentro' => $this->personaIngresoSalidaService->obtenerEstadisticasPersonasDentro(),
            ];
        }, 15);
    }
}
