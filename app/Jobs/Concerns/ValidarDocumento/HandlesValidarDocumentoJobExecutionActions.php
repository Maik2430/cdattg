<?php

namespace App\Jobs\Concerns\ValidarDocumento;

use App\Models\Complementarios\AspiranteComplementario;
use Illuminate\Support\Facades\Log;

trait HandlesValidarDocumentoJobExecutionActions
{
    public function handle(): void
    {
        Log::info("🚀 Iniciando validación de documentos en Google Drive para programa: {$this->complementarioId}");

        $progress = null;
        if ($this->progressId) {
            $progress = \App\Models\Complementarios\SofiaValidationProgress::find($this->progressId);
            if ($progress) {
                $progress->markAsStarted();
                Log::info("📊 Progreso inicializado con ID: {$this->progressId}");
            }
        }

        $aspirantes = AspiranteComplementario::with(['persona.tipoDocumento'])
            ->where('complementario_id', $this->complementarioId)
            ->get();

        if ($aspirantes->isEmpty()) {
            Log::info('ℹ️ No hay aspirantes para validar documentos.');
            if ($progress) {
                $progress->markAsCompleted();
            }

            return;
        }

        $totalAspirantes = $aspirantes->count();
        Log::info("📋 Iniciando validación de documentos para {$totalAspirantes} aspirantes...");

        $exitosos = 0;
        $errores = 0;
        $errores_detalle = [];
        $procesados = 0;

        $batchSize = 10;
        $batches = $aspirantes->chunk($batchSize);

        foreach ($batches as $batchIndex => $batch) {
            Log::info('🔄 Procesando lote '.($batchIndex + 1).'/'.$batches->count()." ({$batch->count()} aspirantes)");

            foreach ($batch as $aspirante) {
                $procesados++;
                $persona = $aspirante->persona;
                $numeroDocumento = $persona->numero_documento;

                try {
                    Log::info("🔍 Validando documento para cédula {$numeroDocumento} ({$procesados}/{$totalAspirantes})");

                    $startTime = microtime(true);
                    $tieneDocumento = $this->validarDocumentoEnDrive($persona);
                    $endTime = microtime(true);
                    $duration = round($endTime - $startTime, 2);

                    $persona->update(['condocumento' => $tieneDocumento ? 1 : 0]);

                    $estadoLabel = $tieneDocumento ? 'Documento encontrado' : 'Documento no encontrado';
                    Log::info("✅ Cédula {$numeroDocumento}: {$estadoLabel} (Tiempo: {$duration}s)");

                    if ($tieneDocumento) {
                        $exitosos++;
                    }

                    if ($progress) {
                        $progress->incrementProcessed($tieneDocumento);
                    }
                } catch (\Exception $e) {
                    $errorMsg = "❌ Error validando documento para cédula {$numeroDocumento}: {$e->getMessage()}";
                    Log::error($errorMsg, [
                        'aspirante_id' => $aspirante->id,
                        'persona_id' => $aspirante->persona_id,
                        'complementario_id' => $this->complementarioId,
                        'exception' => $e->getTraceAsString(),
                    ]);

                    $errores++;
                    $errores_detalle[] = $errorMsg;

                    if ($progress) {
                        $progress->incrementProcessed(false);
                    }
                }

                if ($procesados < $totalAspirantes) {
                    usleep(100000);
                }
            }

            if ($batches->count() > 1 && $batchIndex < $batches->count() - 1) {
                Log::info('🔄 Cambio de lote - Esperando 1 segundo...');
                sleep(1);
            }
        }

        if ($progress) {
            if ($errores > 0) {
                Log::warning("⚠️ Validación de documentos completada con {$errores} errores");
                $progress->markAsFailed($errores_detalle);
            } else {
                Log::info('🎉 Validación de documentos completada exitosamente');
                $progress->markAsCompleted();
            }
        }

        Log::info("📊 Resumen final - Total: {$totalAspirantes}, Con documento: {$exitosos}, Sin documento: ".($totalAspirantes - $exitosos - $errores).", Errores: {$errores}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ValidarDocumentoJob falló: {$exception->getMessage()}", [
            'complementario_id' => $this->complementarioId,
            'user_id' => $this->userId,
            'exception' => $exception,
        ]);
    }
}
