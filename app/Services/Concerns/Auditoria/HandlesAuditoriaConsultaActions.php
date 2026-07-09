<?php

namespace App\Services\Concerns\Auditoria;

use App\Models\Login;

trait HandlesAuditoriaConsultaActions
{
    public function obtenerReporteAuditoria(string $fechaInicio, string $fechaFin, string $tipo = 'todo'): array
    {
        $resultado = [];

        if ($tipo === 'todo' || $tipo === 'logins') {
            $resultado['logins'] = $this->loginRepo->obtenerEstadisticas($fechaInicio, $fechaFin);
        }

        if ($tipo === 'todo' || $tipo === 'asignaciones') {
            $resultado['asignaciones'] = $this->asignacionLogRepo->obtenerAuditoria($fechaInicio, $fechaFin);
        }

        if ($tipo === 'todo' || $tipo === 'senasofiaplus') {
            $resultado['senasofiaplus'] = $this->senasofiaplusLogRepo->obtenerAuditoria($fechaInicio, $fechaFin);
        }

        return $resultado;
    }

    public function detectarActividadesSospechosas(string $fechaInicio, string $fechaFin): array
    {
        $sospechosas = [];

        $loginsFallidos = Login::where('exitoso', false)
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->get()
            ->groupBy('email');

        foreach ($loginsFallidos as $email => $intentos) {
            if ($intentos->count() >= 5) {
                $sospechosas[] = [
                    'tipo' => 'intentos_fallidos',
                    'email' => $email,
                    'intentos' => $intentos->count(),
                    'ultimo_intento' => $intentos->first()->fecha_hora,
                ];
            }
        }

        return $sospechosas;
    }
}
