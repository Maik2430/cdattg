<?php

namespace App\Services\Concerns\PersonaIngresoSalida;

use App\Models\PersonaIngresoSalida;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaIngresoSalidaSalidasPendientesActions
{
    /**
     * Procesa todas las salidas pendientes y genera un reporte
     *
     * @return array Array con información del procesamiento y el reporte generado
     */
    public function procesarSalidasPendientes(): array
    {
        return DB::transaction(function () {
            // Obtener todas las entradas sin salida (pendientes)
            // Solo las del día anterior o anteriores
            $fechaAnterior = Carbon::yesterday();
            $entradasPendientes = PersonaIngresoSalida::whereNull('timestamp_salida')
                ->whereDate('fecha_entrada', '<=', $fechaAnterior->format('Y-m-d'))
                ->with(['persona', 'sede'])
                ->get();

            $salidasProcesadas = [];
            $ahora = Carbon::now();

            foreach ($entradasPendientes as $entrada) {
                // Registrar salida automática
                $entrada->update([
                    'fecha_salida' => $fechaAnterior->format('Y-m-d'),
                    'hora_salida' => '23:59:59',
                    'timestamp_salida' => $fechaAnterior->copy()->setTime(23, 59, 59),
                    'observaciones' => ($entrada->observaciones ?? '').
                        "\n[SALIDA AUTOMÁTICA] Registrada automáticamente por el sistema a la medianoche.",
                    'user_edit_id' => 1, // Sistema
                ]);

                $salidasProcesadas[] = [
                    'persona_ingreso_salida_id' => $entrada->id,
                    'persona_id' => $entrada->persona_id,
                    'persona_nombre' => $entrada->persona
                        ? trim($entrada->persona->primer_nombre.' '.
                            $entrada->persona->segundo_nombre.' '.
                            $entrada->persona->primer_apellido.' '.
                            $entrada->persona->segundo_apellido)
                        : 'Persona no encontrada',
                    'sede_id' => $entrada->sede_id,
                    'sede_nombre' => $entrada->sede ? $entrada->sede->sede : 'Sede no encontrada',
                    'fecha_entrada' => $entrada->fecha_entrada->format('Y-m-d'),
                    'hora_entrada' => $entrada->hora_entrada,
                    'fecha_salida_automatica' => $fechaAnterior->format('Y-m-d'),
                    'hora_salida_automatica' => '23:59:59',
                    'tipo_persona' => $entrada->tipo_persona,
                ];

                Log::info('Salida automática registrada', [
                    'persona_ingreso_salida_id' => $entrada->id,
                    'persona_id' => $entrada->persona_id,
                    'sede_id' => $entrada->sede_id,
                ]);
            }

            // Crear reporte
            $reporte = \App\Models\ReporteSalidaAutomatica::create([
                'fecha_procesamiento' => $ahora->format('Y-m-d'),
                'hora_procesamiento' => $ahora->format('H:i:s'),
                'total_salidas_procesadas' => count($salidasProcesadas),
                'detalle' => json_encode($salidasProcesadas, JSON_UNESCAPED_UNICODE),
                'user_id' => 1, // Sistema
            ]);

            return [
                'total_procesadas' => count($salidasProcesadas),
                'reporte_id' => $reporte->id,
                'salidas' => $salidasProcesadas,
            ];
        });
    }
}
