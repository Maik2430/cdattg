<?php

namespace App\Core\Services\Concerns\Cache;

use Illuminate\Support\Facades\Log;

trait HandlesCacheWarmupActions
{
    /**
     * Pre-carga datos frecuentes
     */
    public function warmup(): void
    {
        Log::info('Cache: Iniciando precarga de datos frecuentes');

        $this->remember('parametros.sistema', function () {
            return \App\Models\Parametro::where('status', true)->get();
        }, null, 'parametros');

        $this->remember('regionales.activas', function () {
            return \App\Models\Regional::where('status', true)->get();
        }, null, 'regionales');

        $this->remember('temas.todos', function () {
            return \App\Models\Tema::with(['parametros' => function ($q) {
                $q->wherePivot('status', 1);
            }])->get();
        }, null, 'temas');

        Log::info('Cache: Precarga completada');
    }
}
