<?php

namespace App\Services\Concerns\Complementarios\AspiranteManagement;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HandlesAspiranteManagementWriteActions
{
    public function agregarAspirante(int $complementarioId, string $numeroDocumento, ?string $observaciones = null): array
    {
        try {
            $programa = $this->programaRepository->findWithRelations($complementarioId);
            if (! $programa) {
                return $this->createErrorResponse(self::PROGRAMA_NO_ENCONTRADO);
            }

            $persona = $this->personaRepository->findByNumeroDocumento($numeroDocumento);
            if (! $persona) {
                return $this->createErrorResponse(
                    'No se encontró ninguna persona registrada con el número de documento "'.$numeroDocumento.'".'
                );
            }

            $existeInscripcion = $this->aspiranteRepository->existeInscripcion($persona->id, $complementarioId);

            if ($existeInscripcion) {
                return $this->createErrorResponse(
                    'El aspirante ya está en este programa complementario.'
                );
            }

            $observacionesFinal = $observaciones ?? 'Agregado manualmente desde gestión de aspirantes';

            $aspirante = $this->aspiranteRepository->create([
                'persona_id' => $persona->id,
                'complementario_id' => $complementarioId,
                'estado' => 1,
                'observaciones' => $observacionesFinal,
            ]);

            Log::info('Aspirante agregado exitosamente', [
                'complementario_id' => $complementarioId,
                'persona_id' => $persona->id,
                'numero_documento' => $numeroDocumento,
                'user_id' => Auth::id(),
            ]);

            $resultado = $this->createSuccessResponse(
                'Aspirante agregado exitosamente. '.$persona->primer_nombre.' '.
                $persona->primer_apellido.' ha sido inscrito en el programa.'
            );
            $resultado['aspirante'] = $aspirante;

            return $resultado;

        } catch (Exception $e) {
            Log::error('Error agregando aspirante: '.$e->getMessage(), [
                'complementario_id' => $complementarioId,
                'numero_documento' => $numeroDocumento,
                'exception' => $e->getTraceAsString(),
            ]);

            return $this->createErrorResponse('Error interno del servidor. Por favor intente nuevamente.');
        }
    }

    public function rechazarAspirante(int $complementarioId, int $aspiranteId, ?string $motivoRechazo = null, ?string $observaciones = null): array
    {
        try {
            $errorResponse = $this->validarRechazarAspirante($complementarioId, $aspiranteId);
            if ($errorResponse !== null) {
                return $errorResponse;
            }

            $aspirantes = $this->aspiranteRepository->findByPrograma($complementarioId);
            $aspirante = $aspirantes->where('id', $aspiranteId)->first();

            if (! $aspirante) {
                return [
                    'success' => false,
                    'message' => 'Aspirante no encontrado.',
                    'status_code' => 200,
                ];
            }

            if (! $aspirante->relationLoaded('persona')) {
                $aspirante->load('persona');
            }

            $personaNombre = $aspirante->persona->primer_nombre.' '.$aspirante->persona->primer_apellido;
            $numeroDocumento = $aspirante->persona->numero_documento;

            $observacionesFinal = $observaciones ?? $aspirante->observaciones;
            if ($motivoRechazo !== null) {
                $observacionesFinal = ($observacionesFinal ? $observacionesFinal.' | ' : '').'Motivo rechazo: '.$motivoRechazo;
            }

            $this->aspiranteRepository->update($aspirante, [
                'estado' => 4,
                'observaciones' => $observacionesFinal,
            ]);

            Log::info('Aspirante rechazado exitosamente', [
                'aspirante_id' => $aspiranteId,
                'complementario_id' => $complementarioId,
                'persona_id' => $aspirante->persona_id,
                'user_id' => Auth::id(),
            ]);

            return $this->createSuccessResponse(
                'Aspirante rechazado exitosamente. '.$personaNombre.' ('.$numeroDocumento.') ha sido marcado como rechazado en el programa.'
            );

        } catch (Exception $e) {
            Log::error('Error rechazando aspirante: '.$e->getMessage(), [
                'complementario_id' => $complementarioId,
                'aspirante_id' => $aspiranteId,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error interno del servidor. Por favor intente nuevamente.',
                'status_code' => 500,
            ];
        }
    }
}
