<?php

namespace App\Services\Concerns\Complementarios\SofiaParametros;

use App\Models\Parametro;
use Exception;
use Illuminate\Support\Facades\Cache;
use Log;

trait HandlesSofiaParametrosCacheHelpers
{
    /**
     * Obtener todos los IDs de parámetros de Sofía
     */
    public static function getAllIds(): array
    {
        if (app()->environment('testing')) {
            return self::obtenerIdsDirectamente();
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            return self::obtenerIdsDirectamente();
        });
    }

    /**
     * Obtener IDs directamente de la base de datos
     */
    private static function obtenerIdsDirectamente(): array
    {
        $parametros = [
            'NO REGISTRADO',
            'REGISTRADO',
            'REQUIERE CAMBIO',
            'VALIDAR',
            'EXITOSO',
            'ERROR',
            'ADVERTENCIA',
            'PENDING',
            'PROCESSING',
            'COMPLETED',
            'FAILED',
        ];

        $ids = [];
        foreach ($parametros as $nombre) {
            $parametro = Parametro::where('name', $nombre)->first();
            if ($parametro) {
                $ids[$nombre] = $parametro->id;
            }
        }

        if (app()->environment('testing')) {
            $faltantes = array_diff($parametros, array_keys($ids));
            if (! empty($faltantes)) {
                self::crearParametrosSiNoExisten();
                foreach ($parametros as $nombre) {
                    if (! isset($ids[$nombre])) {
                        $parametro = Parametro::where('name', $nombre)->first();
                        if ($parametro) {
                            $ids[$nombre] = $parametro->id;
                        }
                    }
                }
            }
        }

        return $ids;
    }

    /**
     * Obtener ID de un parámetro por nombre
     */
    private static function getParametroId(string $nombre): ?int
    {
        if (app()->environment('testing')) {
            $parametro = Parametro::where('name', $nombre)->first();
            if ($parametro) {
                return $parametro->id;
            }

            try {
                self::crearParametrosSiNoExisten();
                $parametro = Parametro::where('name', $nombre)->first();
                if ($parametro) {
                    self::clearCache();

                    return $parametro->id;
                }
            } catch (Exception $e) {
                Log::error('Error al crear parámetros de Sofía en testing: '.$e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            return null;
        }

        $ids = self::getAllIds();

        if (isset($ids[$nombre]) && $ids[$nombre] !== null) {
            return $ids[$nombre];
        }

        $parametro = Parametro::where('name', $nombre)->first();
        if ($parametro) {
            self::clearCache();

            return $parametro->id;
        }

        return null;
    }

    /**
     * Limpiar cache de parámetros
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
