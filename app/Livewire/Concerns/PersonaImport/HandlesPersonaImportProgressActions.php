<?php

namespace App\Livewire\Concerns\PersonaImport;

use App\Models\PersonaImport;
use App\Services\PersonaImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesPersonaImportProgressActions
{
    public function actualizarProgreso(): void
    {
        if (! $this->importacionId) {
            return;
        }

        $importacion = PersonaImport::find($this->importacionId);

        if (! $importacion) {
            $this->mostrarProgreso = false;

            return;
        }

        $importacion->refresh();

        if ($importacion->status === 'pending' && $importacion->created_at->diffInSeconds(now()) > 5) {
            $jobExists = DB::table('jobs')
                ->where('queue', 'long-running')
                ->where('payload', 'like', self::PATRON_PAYLOAD_IMPORT_ID.$importacion->id.'%')
                ->exists();

            if ($jobExists) {
                try {
                    $importService = app(PersonaImportService::class);
                    $importService->procesar($importacion);
                    $importacion->refresh();
                } catch (\Throwable $e) {
                    Log::error('Error procesando importación automáticamente', [
                        'import_id' => $importacion->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->procesados = $importacion->processed_rows ?? 0;
        $this->total = $importacion->total_rows ?? 0;

        if ($this->total === 0 && $this->procesados > 0) {
            $this->total = $this->procesados;
        }

        $this->exitosos = $importacion->success_count ?? 0;
        $this->duplicados = $importacion->duplicate_count ?? 0;
        $this->faltantes = $importacion->missing_contact_count ?? 0;

        $statusLabels = $this->personaImportStatusLabels();
        $statusColors = $this->personaImportStatusColors();

        $this->estado = $statusLabels[$importacion->status] ?? strtoupper($importacion->status);
        $this->estadoColor = $statusColors[$importacion->status] ?? 'secondary';

        $this->issues = $importacion->issues()
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($issue) {
                return [
                    'row_number' => $issue->row_number,
                    'issue_type' => $this->traducirIssueType($issue->issue_type, $issue->error_message),
                    'numero_documento' => $issue->numero_documento,
                    'email' => $issue->email,
                    'celular' => $issue->celular,
                ];
            })
            ->toArray();

        if (in_array($importacion->status, ['completed', 'failed'], true)) {
            $this->mostrarProgreso = false;

            $estadoAnterior = session('import_status_'.$importacion->id);

            if ($estadoAnterior !== $importacion->status) {
                session(['import_status_'.$importacion->id => $importacion->status]);

                if ($importacion->status === 'completed') {
                    $this->dispatch('importacion-completada', [
                        'message' => 'La importación se completó exitosamente.',
                    ]);
                } elseif ($importacion->status === 'failed') {
                    $this->dispatch('importacion-fallida', [
                        'message' => $importacion->error_message ?? 'La importación falló.',
                    ]);
                }
            }

            $this->cargarImportaciones();
        }
    }

    private function resetearProgreso(): void
    {
        $this->procesados = 0;
        $this->total = 0;
        $this->exitosos = 0;
        $this->duplicados = 0;
        $this->faltantes = 0;
        $this->estado = 'PENDIENTE';
        $this->estadoColor = 'secondary';
        $this->issues = [];
    }

    /**
     * @return array<string, string>
     */
    private function personaImportStatusLabels(): array
    {
        return [
            'pending' => 'PENDIENTE',
            'processing' => 'PROCESANDO...',
            'completed' => 'COMPLETADO',
            'failed' => 'FALLIDO',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function personaImportStatusColors(): array
    {
        return [
            'pending' => 'secondary',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
        ];
    }
}
