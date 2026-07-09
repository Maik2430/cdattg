<?php

namespace App\Services\Concerns\Complementarios\SofiaValidation;

use App\Models\Complementarios\AspiranteComplementario;
use App\Models\Complementarios\SofiaValidationProgress;
use Exception;
use Illuminate\Support\Facades\Log;

trait HandlesSofiaValidationValidateActions
{
    /**
     * Validar un aspirante y actualizar su estado
     */
    public function validateAspirante(
        AspiranteComplementario $aspirante,
        int $complementarioId,
        ?SofiaValidationProgress $progress = null
    ): array {
        $cedula = $aspirante->persona->numero_documento;
        $estadoAnterior = $aspirante->persona->estado_sofia;

        try {
            Log::info('Validando cedula', ['cedula' => $cedula]);

            $startTime = microtime(true);
            $resultado = $this->httpClient->validate($cedula);
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            $nuevoEstado = $this->stateMapper->mapToState($resultado);
            $aspirante->persona->update(['estado_sofia' => $nuevoEstado]);

            $estadoLabel = $this->stateMapper->getStateLabel($nuevoEstado);
            Log::info('Cedula validada exitosamente', [
                'cedula' => $cedula,
                'resultado' => $resultado,
                'estado' => $estadoLabel,
                'duration' => $duration,
            ]);

            $this->registerAuditSuccess(
                $aspirante,
                $cedula,
                $resultado,
                $estadoAnterior,
                $nuevoEstado,
                $estadoLabel,
                $duration,
                $complementarioId
            );

            $this->updateProgress($progress, $nuevoEstado);

            return [
                'success' => true,
                'cedula' => $cedula,
                'resultado' => $resultado,
                'estado' => $nuevoEstado,
                'duration' => $duration,
            ];
        } catch (Exception $e) {
            return $this->handleValidationError(
                $e,
                $aspirante,
                $cedula,
                $complementarioId,
                $progress
            );
        }
    }

    /**
     * Obtener aspirantes que necesitan validación
     */
    public function getAspirantesToValidate(int $complementarioId)
    {
        return AspiranteComplementario::with('persona')
            ->where('complementario_id', $complementarioId)
            ->whereHas('persona', function ($query): void {
                $query->whereIn('estado_sofia', [277, 279]);
            })
            ->get();
    }

    /**
     * Verificar que el servicio esté disponible
     */
    public function checkServiceHealth(): void
    {
        $this->httpClient->checkHealth();
    }
}
