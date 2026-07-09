<?php

namespace App\Jobs\Concerns\VerificarPrestamosProximos;

use App\Inventario\Interfaces\Repositories\ParametroTema\ParametroTemaRepositoryInterface;
use App\Models\Inventario\Orden;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait HandlesVerificarPrestamosProximosJobExecutionActions
{
    public function handle(ParametroTemaRepositoryInterface $parametroTemaRepository): void
    {
        try {
            $tipoPrestamoId = $this->obtenerTipoPrestamoId($parametroTemaRepository);
            $estadoAprobadaId = $this->obtenerEstadoAprobadaId($parametroTemaRepository);

            if (! $tipoPrestamoId || ! $estadoAprobadaId) {
                Log::warning('[VerificarPrestamosProximosJob] No se encontraron tipos o estados necesarios');

                return;
            }

            $ordenesTotales = 0;

            foreach ([3, 2, 1] as $diasAntes) {
                $fechaObjetivo = Carbon::today()->addDays($diasAntes);

                $ordenes = Orden::with(['detalles.producto', 'detalles.devoluciones', 'userCreate'])
                    ->where('tipo_orden_id', $tipoPrestamoId)
                    ->whereDate('fecha_devolucion', $fechaObjetivo)
                    ->whereNotNull('fecha_devolucion')
                    ->whereHas('detalles', function ($query) use ($estadoAprobadaId): void {
                        $query->where('estado_orden_id', $estadoAprobadaId);
                    })
                    ->get();

                foreach ($ordenes as $orden) {
                    $this->procesarOrden($orden, $diasAntes);
                }

                $ordenesTotales += $ordenes->count();

                Log::info('[VerificarPrestamosProximosJob] Verificación para '.$diasAntes.' días', [
                    'ordenes_procesadas' => $ordenes->count(),
                    'fecha_objetivo' => $fechaObjetivo->format('Y-m-d'),
                    'dias_restantes' => $diasAntes,
                ]);
            }

            Log::info('[VerificarPrestamosProximosJob] Verificación completada', [
                'ordenes_totales' => $ordenesTotales,
            ]);
        } catch (\Exception $e) {
            Log::error('[VerificarPrestamosProximosJob] Error en la verificación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
