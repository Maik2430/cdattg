<?php

namespace App\Jobs;

use App\Services\CarnetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GenerarCarnetsMasivosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Collection $aprendices;

    public $tries = 2;

    public $timeout = 1800; // 30 minutos

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $aprendices)
    {
        $this->aprendices = $aprendices;
    }

    /**
     * Execute the job.
     */
    public function handle(CarnetService $carnetService): void
    {
        try {
            Log::info('Iniciando generación masiva de carnets', [
                'total' => $this->aprendices->count(),
            ]);

            $generados = 0;
            $fallidos = 0;

            foreach ($this->aprendices as $aprendiz) {
                try {
                    $carnetService->generarCarnetAprendiz($aprendiz);
                    $generados++;
                } catch (\Exception $e) {
                    $fallidos++;
                    Log::error('Error generando carnet individual', [
                        'aprendiz_id' => $aprendiz->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Generación masiva de carnets completada', [
                'generados' => $generados,
                'fallidos' => $fallidos,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en generación masiva de carnets', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Fallo definitivo en generación masiva de carnets', [
            'total' => $this->aprendices->count(),
            'error' => $exception->getMessage(),
        ]);
    }
}
