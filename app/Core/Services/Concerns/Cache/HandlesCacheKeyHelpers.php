<?php

namespace App\Core\Services\Concerns\Cache;

trait HandlesCacheKeyHelpers
{
    /**
     * Genera clave de caché con prefijo
     *
     * @param  mixed  ...$parts
     */
    public function key(string $prefix, ...$parts): string
    {
        $key = $prefix;
        foreach ($parts as $part) {
            if (is_array($part)) {
                $key .= '.'.md5(json_encode($part));
            } else {
                $key .= '.'.$part;
            }
        }

        return $key;
    }

    /**
     * Obtiene estadísticas de caché
     */
    public function getStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
        ];
    }
}
