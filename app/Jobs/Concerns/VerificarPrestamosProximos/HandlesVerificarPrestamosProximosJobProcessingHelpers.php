<?php

namespace App\Jobs\Concerns\VerificarPrestamosProximos;

use App\Inventario\Interfaces\Repositories\ParametroTema\ParametroTemaRepositoryInterface;
use App\Models\Inventario\Orden;
use App\Notifications\RecordatorioDevolucionNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesVerificarPrestamosProximosJobProcessingHelpers
{
    private function procesarOrden(Orden $orden, int $diasAntes): void
    {
        try {
            $tienePendientes = false;
            foreach ($orden->detalles as $detalle) {
                if ($detalle->getCantidadPendiente() > 0) {
                    $tienePendientes = true;
                    break;
                }
            }

            if (! $tienePendientes) {
                return;
            }

            if (! $orden->userCreate) {
                return;
            }

            if ($this->yaSeEnvioNotificacionHoy($orden, $diasAntes)) {
                return;
            }

            $orden->userCreate->notify(new RecordatorioDevolucionNotification($orden, $diasAntes));

            Log::info('[VerificarPrestamosProximosJob] Notificación enviada', [
                'orden_id' => $orden->id,
                'usuario_id' => $orden->userCreate->id,
                'usuario_email' => $orden->userCreate->email,
                'fecha_devolucion' => $orden->fecha_devolucion->format('Y-m-d'),
                'dias_restantes' => $diasAntes,
            ]);
        } catch (\Exception $e) {
            Log::error('[VerificarPrestamosProximosJob] Error al procesar orden', [
                'orden_id' => $orden->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function obtenerTipoPrestamoId(ParametroTemaRepositoryInterface $parametroTemaRepository): ?int
    {
        $parametroTema = $parametroTemaRepository->obtenerEstadoPorNombre('PRÉSTAMO', 'TIPOS DE ORDEN');

        return $parametroTema?->id;
    }

    private function obtenerEstadoAprobadaId(ParametroTemaRepositoryInterface $parametroTemaRepository): ?int
    {
        $parametroTema = $parametroTemaRepository->obtenerEstadoPorNombre('APROBADA', 'ESTADOS DE ORDEN');

        return $parametroTema?->id;
    }

    private function yaSeEnvioNotificacionHoy(Orden $orden, int $diasAntes): bool
    {
        if (! $orden->userCreate) {
            return false;
        }

        try {
            return DB::table('notificaciones')
                ->where('notificable_type', 'App\\Models\\User')
                ->where('notificable_id', $orden->userCreate->id)
                ->where('tipo', 'App\\Notifications\\RecordatorioDevolucionNotification')
                ->whereDate('created_at', Carbon::today())
                ->whereRaw("JSON_EXTRACT(datos, '$.orden_id') = ?", [$orden->id])
                ->whereRaw("JSON_EXTRACT(datos, '$.dias_restantes') = ?", [$diasAntes])
                ->exists();
        } catch (\Exception $e) {
            Log::warning('[VerificarPrestamosProximosJob] Error al verificar duplicados', [
                'error' => $e->getMessage(),
                'orden_id' => $orden->id,
                'dias_antes' => $diasAntes,
            ]);

            return false;
        }
    }
}
