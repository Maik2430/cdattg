<?php

namespace App\Core\Services\Concerns\Cache;

trait DefinesCacheConstants
{
    /**
     * Tiempo de caché por defecto en minutos
     */
    const DEFAULT_TTL = 60;

    /**
     * Tiempos de caché personalizados por tipo
     */
    const TTL_CONFIG = [
        'parametros' => 1440,
        'temas' => 1440,
        'regionales' => 720,
        'programas' => 360,
        'fichas' => 60,
        'aprendices' => 30,
        'instructores' => 30,
        'asistencias' => 5,
        'estadisticas' => 15,
    ];
}
