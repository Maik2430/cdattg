<?php

namespace App\Observers\Concerns\AspiranteComplementario;

use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Complementarios\ComplementarioOfertado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteComplementarioCreatedActions
{
    public function created(AspiranteComplementario $aspirante): void
    {
        try {
            $complementario = ComplementarioOfertado::find($aspirante->complementario_id);

            if (! $complementario) {
                Log::warning('No se pudo cargar el complementario para el aspirante', [
                    'aspirante_id' => $aspirante->id,
                    'complementario_id' => $aspirante->complementario_id,
                ]);

                return;
            }

            if ($complementario->estado !== 1) {
                Log::debug('Complementario no está en estado "Con Oferta", no se creará nuevo complementario', [
                    'complementario_id' => $complementario->id,
                    'estado' => $complementario->estado,
                ]);

                return;
            }

            $totalAspirantes = $complementario->aspirantes()->count();

            Log::debug('Verificando cupos del complementario', [
                'complementario_id' => $complementario->id,
                'complementario_codigo' => $complementario->codigo,
                'total_aspirantes' => $totalAspirantes,
                'cupos' => $complementario->cupos,
                'aspirante_id' => $aspirante->id,
            ]);

            if ($totalAspirantes >= $complementario->cupos) {
                DB::transaction(function () use ($complementario, $totalAspirantes): void {
                    $complementarioActualizado = ComplementarioOfertado::lockForUpdate()->find($complementario->id);

                    if (! $complementarioActualizado) {
                        throw new \Exception('No se pudo bloquear el complementario para actualización');
                    }

                    if ($complementarioActualizado->estado !== 1) {
                        Log::info('El complementario cambió de estado durante la transacción, cancelando creación de nuevo complementario', [
                            'complementario_id' => $complementarioActualizado->id,
                            'estado_actual' => $complementarioActualizado->estado,
                        ]);

                        return;
                    }

                    $estadoCuposLlenosId = $this->obtenerEstadoIdLegacy(2);

                    $complementarioActualizado->update(['estado_id' => $estadoCuposLlenosId]);

                    $nuevoComplementario = $this->crearNuevoComplementario($complementarioActualizado);

                    Log::info('Nuevo complementario creado automáticamente al llenarse los cupos', [
                        'complementario_original_id' => $complementarioActualizado->id,
                        'complementario_original_codigo' => $complementarioActualizado->codigo,
                        'nuevo_complementario_id' => $nuevoComplementario->id,
                        'nuevo_complementario_codigo' => $nuevoComplementario->codigo,
                        'total_aspirantes' => $totalAspirantes,
                        'cupos' => $complementarioActualizado->cupos,
                        'origen' => 'Observer - AspiranteComplementario created',
                    ]);
                });
            }
        } catch (\Exception $e) {
            Log::error('Error en AspiranteComplementarioObserver al verificar cupos', [
                'aspirante_id' => $aspirante->id,
                'complementario_id' => $aspirante->complementario_id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
