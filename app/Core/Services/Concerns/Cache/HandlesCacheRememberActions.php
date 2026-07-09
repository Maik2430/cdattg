<?php

namespace App\Core\Services\Concerns\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HandlesCacheRememberActions
{
    /**
     * Obtiene datos de caché o ejecuta el callback
     *
     * @param  int|null  $ttl  Tiempo en minutos
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null, ?string $tipo = null)
    {
        $ttl = $ttl ?? ($tipo ? self::TTL_CONFIG[$tipo] ?? self::DEFAULT_TTL : self::DEFAULT_TTL);

        return Cache::remember($key, now()->addMinutes($ttl), function () use ($callback, $key) {
            if (app()->environment('local')) {
                Log::debug("Cache MISS: {$key}");
            }

            return $callback();
        });
    }

    /**
     * Obtiene datos de caché o ejecuta el callback con tags
     *
     * @return mixed
     */
    public function rememberWithTags(array $tags, string $key, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;

        if (config('cache.default') === 'file') {
            return $this->remember($key, $callback, $ttl);
        }

        return Cache::tags($tags)->remember($key, now()->addMinutes($ttl), $callback);
    }
}
