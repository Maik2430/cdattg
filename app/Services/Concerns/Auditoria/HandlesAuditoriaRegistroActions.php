<?php

namespace App\Services\Concerns\Auditoria;

use Illuminate\Support\Facades\Log;

trait HandlesAuditoriaRegistroActions
{
    public function registrarLogin(array $datos): void
    {
        try {
            $this->loginRepo->registrar($datos);

            if (! $datos['exitoso']) {
                Log::warning('Intento de login fallido', [
                    'email' => $datos['email'],
                    'ip' => $datos['ip_address'] ?? request()->ip(),
                ]);

                $intentosFallidos = $this->loginRepo->contarIntentosFallidosRecientes($datos['email']);

                if ($intentosFallidos >= 5) {
                    Log::alert('Múltiples intentos fallidos detectados', [
                        'email' => $datos['email'],
                        'intentos' => $intentosFallidos,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error registrando login', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function registrarCambioAsignacion(int $instructorId, int $fichaId, string $accion, array $detalles = []): void
    {
        try {
            $this->asignacionLogRepo->registrar([
                'instructor_id' => $instructorId,
                'ficha_caracterizacion_id' => $fichaId,
                'accion' => $accion,
                'detalles' => json_encode($detalles),
                'user_id' => auth()->id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error registrando cambio de asignación', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function registrarValidacionSenasofiaplus(int $aspiranteId, string $resultado, string $mensaje, array $detalles = []): void
    {
        try {
            $this->senasofiaplusLogRepo->registrar([
                'aspirante_id' => $aspiranteId,
                'accion' => 'validar',
                'detalles' => $detalles,
                'resultado' => $resultado,
                'mensaje' => $mensaje,
                'user_id' => 1,
                'fecha_accion' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error registrando validación SenaSofiaPlus', [
                'error' => $e->getMessage(),
                'aspirante_id' => $aspiranteId,
            ]);
        }
    }
}
