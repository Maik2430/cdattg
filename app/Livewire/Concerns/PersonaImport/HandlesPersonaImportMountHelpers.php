<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Jobs\ProcessPersonaImportJob;
use App\Models\PersonaImport;
use App\Services\PersonaImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaImportMountHelpers
{
    public function mount(): void
    {
        $this->cargarImportaciones();
        $this->procesarPendientes();
        $this->verificarPlantilla();
    }

    /**
     * Verifica si la plantilla de importación está disponible
     */
    private function verificarPlantilla(): void
    {
        $rutaPlantilla = public_path('storage/plantillas/personas_masivo.xlsx');
        $this->plantillaDisponible = file_exists($rutaPlantilla);
    }

    /**
     * Procesa importaciones pendientes si el queue está en modo sync
     * o si hay importaciones que quedaron pendientes sin procesar
     */
    private function procesarPendientes(): void
    {
        $pendientes = PersonaImport::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24))
            ->get();

        foreach ($pendientes as $importacion) {
            if (config('queue.default') === 'sync') {
                try {
                    $importService = app(PersonaImportService::class);
                    $importService->procesar($importacion);
                } catch (\Throwable $e) {
                    Log::warning('Procesamiento sync falló, encolando importación', [
                        'import_id' => $importacion->id,
                        'error' => $e->getMessage(),
                    ]);
                    ProcessPersonaImportJob::dispatch($importacion->id);
                }
            } else {
                $jobExists = DB::table('jobs')
                    ->where('queue', 'long-running')
                    ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID.$importacion->id.'%')
                    ->exists();

                if (! $jobExists) {
                    ProcessPersonaImportJob::dispatch($importacion->id);

                    if ($importacion->created_at->diffInSeconds(now()) > 10) {
                        try {
                            $importService = app(PersonaImportService::class);
                            $importService->procesar($importacion);
                        } catch (\Throwable $e) {
                            Log::error('Error procesando importación pendiente', [
                                'import_id' => $importacion->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
