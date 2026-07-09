<?php

namespace App\Core\Services\Concerns\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait HandlesCacheInvalidationActions
{
    /**
     * Invalida caché por clave
     */
    public function forget(string $key): bool
    {
        if (app()->environment('local')) {
            Log::debug("Cache FORGET: {$key}");
        }

        return Cache::forget($key);
    }

    /**
     * Invalida caché por tags
     */
    public function flushTags(array $tags): bool
    {
        if (config('cache.default') === 'file') {
            foreach ($tags as $tag) {
                Cache::forget($tag.'.*');
            }

            return true;
        }

        if (app()->environment('local')) {
            Log::debug('Cache FLUSH TAGS: '.implode(', ', $tags));
        }

        return Cache::tags($tags)->flush();
    }

    /**
     * Limpia toda la caché
     */
    public function flush(): bool
    {
        Log::info('Cache: Limpieza completa de caché');

        return Cache::flush();
    }
}
