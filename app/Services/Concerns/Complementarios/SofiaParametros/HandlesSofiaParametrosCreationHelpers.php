<?php

namespace App\Services\Concerns\Complementarios\SofiaParametros;

use App\Models\Parametro;
use App\Models\Tema;
use Exception;
use Log;

trait HandlesSofiaParametrosCreationHelpers
{
    /**
     * Crear parámetros de Sofía si no existen (público para testing)
     */
    public static function crearParametrosSiNoExisten(): void
    {
        $userId = null;

        try {
            $temaEstados = Tema::updateOrCreate(
                ['name' => 'ESTADOS SOFIA'],
                ['status' => 1, 'user_create_id' => $userId]
            );

            $temaAcciones = Tema::updateOrCreate(
                ['name' => 'ACCIONES SOFIA'],
                ['status' => 1, 'user_create_id' => $userId]
            );

            $temaResultados = Tema::updateOrCreate(
                ['name' => 'RESULTADOS VALIDACION SOFIA'],
                ['status' => 1, 'user_create_id' => $userId]
            );

            $temaProgreso = Tema::updateOrCreate(
                ['name' => 'ESTADOS PROGRESO SOFIA'],
                ['status' => 1, 'user_create_id' => $userId]
            );

            $parametrosEstados = ['NO REGISTRADO', 'REGISTRADO', 'REQUIERE CAMBIO'];
            foreach ($parametrosEstados as $nombre) {
                $parametro = Parametro::updateOrCreate(
                    ['name' => $nombre],
                    [
                        'status' => 1,
                        'user_create_id' => $userId,
                    ]
                );
                $temaEstados->parametros()->syncWithoutDetaching([
                    $parametro->id => ['status' => 1, 'user_create_id' => $userId],
                ]);
            }

            $parametroAccion = Parametro::updateOrCreate(
                ['name' => 'VALIDAR'],
                ['status' => 1, 'user_create_id' => $userId]
            );
            $temaAcciones->parametros()->syncWithoutDetaching([
                $parametroAccion->id => ['status' => 1, 'user_create_id' => $userId],
            ]);

            $parametrosResultados = ['EXITOSO', 'ERROR', 'ADVERTENCIA'];
            foreach ($parametrosResultados as $nombre) {
                $parametro = Parametro::updateOrCreate(
                    ['name' => $nombre],
                    ['status' => 1, 'user_create_id' => $userId]
                );
                $temaResultados->parametros()->syncWithoutDetaching([
                    $parametro->id => ['status' => 1, 'user_create_id' => $userId],
                ]);
            }

            $parametrosProgreso = ['PENDING', 'PROCESSING', 'COMPLETED', 'FAILED'];
            foreach ($parametrosProgreso as $nombre) {
                $parametro = Parametro::updateOrCreate(
                    ['name' => $nombre],
                    [
                        'status' => 1,
                        'user_create_id' => $userId,
                    ]
                );
                $temaProgreso->parametros()->syncWithoutDetaching([
                    $parametro->id => ['status' => 1, 'user_create_id' => $userId],
                ]);
            }

            self::clearCache();
        } catch (Exception $e) {
            Log::error('Error al crear parámetros de Sofía: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
