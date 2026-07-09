<?php

namespace App\Services\Concerns\Complementarios\SofiaValidation;

use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Complementarios\SofiaValidationProgress;
use Exception;
use Illuminate\Support\Facades\Log;

trait HandlesSofiaValidationAuditHelpers
{
    /**
     * Registrar auditoría de éxito
     */
    private function registerAuditSuccess(
        AspiranteComplementario $aspirante,
        string $cedula,
        string $resultado,
        int $estadoAnterior,
        int $nuevoEstado,
        string $estadoLabel,
        float $duration,
        int $complementarioId
    ): void {
        $resultadoAuditoria = $this->getAuditResult($nuevoEstado);
        $this->auditoriaService->registrarValidacionSenasofiaplus(
            $aspirante->id,
            $resultadoAuditoria,
            "Validacion completada: {$resultado} -> {$estadoLabel}",
            [
                'cedula' => $cedula,
                'resultado_api' => $resultado,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $nuevoEstado,
                'tiempo_respuesta' => $duration,
                'complementario_id' => $complementarioId,
            ]
        );
    }

    /**
     * Obtener resultado de auditoría
     */
    private function getAuditResult(int $estado): string
    {
        return match ($estado) {
            278 => 'exitoso',
            277 => 'advertencia',
            279 => 'exitoso',
            default => 'advertencia',
        };
    }

    /**
     * Actualizar progreso
     */
    private function updateProgress(?SofiaValidationProgress $progress, int $nuevoEstado): void
    {
        if ($progress) {
            $isSuccessful = in_array($nuevoEstado, [277, 278, 279], true);
            $progress->incrementProcessed($isSuccessful);
        }
    }

    /**
     * Manejar error de validación
     */
    private function handleValidationError(
        Exception $e,
        AspiranteComplementario $aspirante,
        string $cedula,
        int $complementarioId,
        ?SofiaValidationProgress $progress
    ): array {
        $errorMsg = "Error con cedula {$cedula}: {$e->getMessage()}";
        Log::error('Error validando cedula', [
            'aspirante_id' => $aspirante->id,
            'persona_id' => $aspirante->persona_id,
            'complementario_id' => $complementarioId,
            'cedula' => $cedula,
            'exception' => $e->getTraceAsString(),
        ]);

        $this->auditoriaService->registrarValidacionSenasofiaplus(
            $aspirante->id,
            'error',
            $errorMsg,
            [
                'cedula' => $cedula,
                'complementario_id' => $complementarioId,
                'exception_message' => $e->getMessage(),
                'exception_type' => get_class($e),
            ]
        );

        if ($progress) {
            $progress->incrementProcessed(false);
        }

        return [
            'success' => false,
            'cedula' => $cedula,
            'error' => $errorMsg,
        ];
    }
}
