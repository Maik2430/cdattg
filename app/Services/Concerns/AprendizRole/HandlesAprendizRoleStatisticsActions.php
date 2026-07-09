<?php

namespace App\Services\Concerns\AprendizRole;

use App\Models\Aprendiz;
use Illuminate\Support\Facades\Log;

trait HandlesAprendizRoleStatisticsActions
{
    public function getRoleStatistics(): array
    {
        try {
            $totalAprendices = Aprendiz::count();
            $aprendicesConUsuario = Aprendiz::whereHas('persona.user')->count();
            $aprendicesConRol = Aprendiz::whereHas('persona.user', function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'APRENDIZ');
                });
            })->count();

            return [
                'total_aprendices' => $totalAprendices,
                'con_usuario' => $aprendicesConUsuario,
                'con_rol_aprendiz' => $aprendicesConRol,
                'sin_usuario' => $totalAprendices - $aprendicesConUsuario,
                'sin_rol_aprendiz' => $aprendicesConUsuario - $aprendicesConRol,
                'porcentaje_con_rol' => $totalAprendices > 0 ? round(($aprendicesConRol / $totalAprendices) * 100, 2) : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de roles', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
